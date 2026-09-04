<?php

namespace Tests\Feature;

use App\Models\CareProcedureLog;
use App\Models\CareSession;
use App\Models\Course;
use App\Models\MasterSdki;
use App\Models\MasterSiki;
use App\Models\MasterSlki;
use App\Models\MasterSpoProcedure;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $dosen;
    protected User $mahasiswa;
    protected User $admin;
    protected Course $course;
    protected CareSession $session;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->dosen = User::where('role', 'dosen')->first();
        $this->mahasiswa = User::where('role', 'mahasiswa')->first();
        $this->admin = User::where('role', 'admin')->first();
        $this->course = Course::first();

        // Create sample care session
        $this->session = CareSession::create([
            'student_id'        => $this->mahasiswa->id,
            'course_id'         => $this->course->id,
            'mentor_dosen_id'   => $this->dosen->id,
            'patient_name'      => 'Tn. Ahmad Fauzi (Test Pasien)',
            'medical_record_no' => 'RM-TEST-999',
            'age'               => 45,
            'gender'            => 'L',
            'triage_category'   => 'merah',
            'status'            => 'submitted',
            'submitted_at'      => now(),
        ]);
    }

    public function test_guest_can_see_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('e-Askep Web Portal');
    }

    public function test_dosen_login_redirects_to_dosen_dashboard(): void
    {
        $response = $this->post('/login', [
            'email'    => $this->dosen->email,
            'password' => 'dosen123',
        ]);

        $response->assertRedirect(route('dosen.dashboard'));
        $this->assertAuthenticatedAs($this->dosen);
    }

    public function test_mahasiswa_login_redirects_to_mahasiswa_dashboard(): void
    {
        $response = $this->post('/login', [
            'email'    => $this->mahasiswa->email,
            'password' => 'mhs123',
        ]);

        $response->assertRedirect(route('mahasiswa.dashboard'));
        $this->assertAuthenticatedAs($this->mahasiswa);
    }

    public function test_dosen_can_view_dashboard_and_review_desk(): void
    {
        $this->actingAs($this->dosen);

        $dashResponse = $this->get(route('dosen.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Meja Telaah Dosen');
        $dashResponse->assertSee('Tn. Ahmad Fauzi');

        $reviewResponse = $this->get(route('dosen.review', $this->session->uuid));
        $reviewResponse->assertStatus(200);
        $reviewResponse->assertSee('RM-TEST-999');
        $reviewResponse->assertSee('Rubrik Penilaian Sub-CPMK');
    }

    public function test_dosen_can_batch_verify_spo_procedures(): void
    {
        $this->actingAs($this->dosen);

        $spo = MasterSpoProcedure::first();
        $log = CareProcedureLog::create([
            'care_session_id' => $this->session->id,
            'procedure_id'    => $spo->id,
            'is_performed'    => true,
            'is_verified'     => false,
        ]);

        $response = $this->post(route('dosen.verify-spo', $this->session->uuid), [
            'procedure_ids' => [$log->id],
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('care_procedure_logs', [
            'id'          => $log->id,
            'is_verified' => true,
        ]);
    }

    public function test_dosen_can_request_revision(): void
    {
        $this->actingAs($this->dosen);

        $response = $this->post(route('dosen.request-revision', $this->session->uuid), [
            'revision_notes' => 'Harap lengkapi data analisa gas darah dan auskultasi paru.',
        ]);

        $response->assertRedirect(route('dosen.dashboard'));
        $this->assertDatabaseHas('care_sessions', [
            'id'     => $this->session->id,
            'status' => 'need_revision',
        ]);
    }

    public function test_dosen_can_approve_and_grade_session(): void
    {
        $this->actingAs($this->dosen);

        $response = $this->post(route('dosen.approve-grade', $this->session->uuid), [
            'score_pengkajian' => 90.0,
            'score_diagnosa'   => 88.0,
            'score_prosedur'   => 92.0,
            'score_evaluasi'   => 85.0,
            'general_notes'    => 'Kinerja asuhan klinis sangat memuaskan.',
        ]);

        $response->assertRedirect(route('dosen.dashboard'));
        $this->assertDatabaseHas('care_sessions', [
            'id'     => $this->session->id,
            'status' => 'approved_graded',
        ]);
        $this->assertDatabaseHas('session_reviews', [
            'care_session_id' => $this->session->id,
            'final_score'     => 89.10, // 90*0.25 + 88*0.25 + 92*0.30 + 85*0.20 = 22.5 + 22.0 + 27.6 + 17.0 = 89.1
        ]);
    }

    public function test_mahasiswa_can_create_new_care_session(): void
    {
        $this->actingAs($this->mahasiswa);

        $response = $this->post(route('mahasiswa.store'), [
            'course_id'         => $this->course->id,
            'mentor_dosen_id'   => $this->dosen->id,
            'patient_name'      => 'Ny. Halimah (Baru)',
            'medical_record_no' => 'RM-WEB-001',
            'age'               => 30,
            'gender'            => 'P',
            'triage_category'   => 'hijau',
        ]);

        $session = CareSession::where('medical_record_no', 'RM-WEB-001')->first();
        $this->assertNotNull($session);
        $response->assertRedirect(route('mahasiswa.show', $session->uuid));
    }

    public function test_mahasiswa_can_add_3s_care_plan(): void
    {
        $this->actingAs($this->mahasiswa);
        $this->session->update(['status' => 'draft']);

        $sdki = MasterSdki::first();
        $slki = MasterSlki::first();
        $siki = MasterSiki::first();

        $response = $this->post(route('mahasiswa.store-care-plan', $this->session->uuid), [
            'master_sdki_id'   => $sdki->id,
            'master_slki_id'   => $slki->id,
            'master_siki_id'   => $siki->id,
            'subjective_data'  => 'Pasien mengeluh sesak napas berat',
            'objective_data'   => 'RR 30x/m, wheezing (+)',
            'priority_order'   => 1,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('nursing_care_plans', [
            'care_session_id' => $this->session->id,
            'sdki_id'         => $sdki->id,
            'priority_order'  => 1,
        ]);
    }

    public function test_submitted_session_is_locked_from_student_modification(): void
    {
        $this->actingAs($this->mahasiswa);
        $this->session->update(['status' => 'submitted']);

        $sdki = MasterSdki::first();
        $slki = MasterSlki::first();
        $siki = MasterSiki::first();

        $response = $this->post(route('mahasiswa.store-care-plan', $this->session->uuid), [
            'master_sdki_id'   => $sdki->id,
            'master_slki_id'   => $slki->id,
            'master_siki_id'   => $siki->id,
            'priority_order'   => 1,
        ]);

        $response->assertStatus(403);
    }

    public function test_print_page_renders_clean_a4_layout(): void
    {
        $this->actingAs($this->dosen);

        $response = $this->get(route('print.case', $this->session->uuid));
        $response->assertStatus(200);
        $response->assertSee('Politeknik Kesehatan Kemenkes Riau');
        $response->assertSee('LEMBAR PENGKAJIAN & ASUHAN KEPERAWATAN KLINIS', false);
        $response->assertSee('Tn. Ahmad Fauzi');
    }

    public function test_admin_can_access_admin_dashboard_and_courses(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Administrator Akademik');

        $coursesResponse = $this->get(route('admin.courses'));
        $coursesResponse->assertStatus(200);
        $coursesResponse->assertSee('Katalog Stase', false);
        $coursesResponse->assertSee('Keperawatan Gawat Darurat');
    }
}
