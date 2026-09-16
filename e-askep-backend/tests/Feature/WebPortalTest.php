<?php

namespace Tests\Feature;

use App\Models\CareProcedureLog;
use App\Models\CareSession;
use App\Models\Course;
use App\Models\MasterSdki;
use App\Models\MasterSiki;
use App\Models\MasterSlki;
use App\Models\MasterSpoProcedure;
use App\Models\SessionReview;
use App\Models\User;
use App\Models\VitalSignMonitoring;
use App\Support\PenilaianInstrument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Inertia\Testing\AssertableInertia as Assert;

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
        $response->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
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
        $dashResponse->assertInertia(fn (Assert $page) => $page->component('Dosen/Dashboard')->has('sessions'));

        $reviewResponse = $this->get(route('dosen.review', $this->session->uuid));
        $reviewResponse->assertStatus(200);
        $reviewResponse->assertInertia(fn (Assert $page) => $page->component('Dosen/Review')->has('session'));
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

    public function test_dosen_can_approve_and_grade_session_via_instrument(): void
    {
        $this->actingAs($this->dosen);

        // Stase KGD: 15 aspek, skor penuh 4 pada setiap aspek.
        $scores = array_fill_keys(range(1, 15), 4);

        $response = $this->post(route('dosen.approve-grade', $this->session->uuid), [
            'instrument_scores' => $scores,
            'instrument_notes'  => [1 => 'Identitas pasien lengkap dan terverifikasi.'],
            'general_notes'     => 'Kinerja asuhan klinis sangat memuaskan.',
        ]);

        $response->assertRedirect(route('dosen.dashboard'));
        $this->assertDatabaseHas('care_sessions', [
            'id'     => $this->session->id,
            'status' => 'approved_graded',
        ]);

        // Skor penuh pada seluruh aspek: tiap kelompok rubrik bernilai 100,
        // sehingga nilai akhir berbobot juga 100.
        $review = SessionReview::where('care_session_id', $this->session->id)->firstOrFail();
        $this->assertEquals(100.00, (float) $review->final_score);
        $this->assertSame(60, $review->rubric_scores['instrument']['total_score']);
        $this->assertSame(60, $review->rubric_scores['instrument']['max_score']);
        $this->assertSame('Kompeten', $review->rubric_scores['instrument']['kategori']);
        $this->assertSame('kgd', $review->rubric_scores['instrument']['stage_type']);
    }

    public function test_instrument_scores_drive_the_sub_cpmk_rubric(): void
    {
        $this->actingAs($this->dosen);

        $definition = PenilaianInstrument::for('kgd');

        // Aspek pengkajian diberi skor 2, seluruh aspek lain skor 4.
        $scores = [];
        foreach ($definition['items'] as $item) {
            $scores[$item['no']] = $item['group'] === 'pengkajian' ? 2 : 4;
        }

        $this->post(route('dosen.approve-grade', $this->session->uuid), [
            'instrument_scores' => $scores,
        ])->assertRedirect(route('dosen.dashboard'));

        $review = SessionReview::where('care_session_id', $this->session->id)->firstOrFail();

        $this->assertEquals(50.0, $review->rubric_scores['pengkajian']);
        $this->assertEquals(100.0, $review->rubric_scores['diagnosa']);
        $this->assertEquals(100.0, $review->rubric_scores['prosedur']);
        $this->assertEquals(100.0, $review->rubric_scores['evaluasi']);

        // 50*0.25 + 100*0.25 + 100*0.30 + 100*0.20 = 87.5
        $this->assertEquals(87.50, (float) $review->final_score);
    }

    public function test_approve_and_grade_rejects_incomplete_instrument(): void
    {
        $this->actingAs($this->dosen);

        // KGD memerlukan 15 aspek; hanya 3 yang dikirim.
        $response = $this->post(route('dosen.approve-grade', $this->session->uuid), [
            'instrument_scores' => [1 => 4, 2 => 3, 3 => 4],
        ]);

        $response->assertSessionHasErrors('instrument_scores');
        $this->assertDatabaseHas('care_sessions', [
            'id'     => $this->session->id,
            'status' => 'submitted',
        ]);
    }

    public function test_review_page_provides_the_matching_clinical_instrument(): void
    {
        $this->actingAs($this->dosen);

        $this->get(route('dosen.review', $this->session->uuid))
            ->assertStatus(200)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dosen/Review')
                ->where('instrument.stage_type', 'kgd')
                ->where('instrument.max_score', 60)
                ->where('instrument.passing_score', 75)
                ->has('instrument.items', 15)
                ->where('instrument.identity.Nama Mahasiswa', $this->mahasiswa->name)
            );
    }

    public function test_dosen_can_export_instrument_as_pdf(): void
    {
        $this->actingAs($this->dosen);

        $response = $this->get(route('dosen.instrumen.pdf', $this->session->uuid) . '?' . http_build_query([
            'scores' => array_fill_keys(range(1, 15), 3),
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_dosen_can_export_instrument_as_word(): void
    {
        $this->actingAs($this->dosen);

        $response = $this->get(route('dosen.instrumen.word', $this->session->uuid) . '?' . http_build_query([
            'scores' => array_fill_keys(range(1, 15), 3),
        ]));

        $response->assertStatus(200);
        $response->assertHeader(
            'content-type',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        );

        // Berkas harus berupa paket OOXML yang sah dan memuat aspek instrumen.
        $binary = $response->getContent();
        $this->assertSame("PK\x03\x04", substr($binary, 0, 4));

        $path = tempnam(sys_get_temp_dir(), 'docx_test_');
        file_put_contents($path, $binary);

        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($path) === true);
        $document = $zip->getFromName('word/document.xml');
        $zip->close();
        @unlink($path);

        $this->assertNotFalse($document);
        $this->assertStringContainsString('INSTRUMEN PENILAIAN PENGKAJIAN PROSES KEPERAWATAN GAWAT DARURAT', $document);
        $this->assertStringContainsString('Survei primer (Airway) dikaji dan didokumentasikan sesuai prosedur', $document);
        $this->assertStringContainsString('Total Skor (maksimal 60)', $document);
    }

    public function test_student_cannot_export_instrument_before_it_is_graded(): void
    {
        $this->actingAs($this->mahasiswa);

        $this->get(route('mahasiswa.instrumen.pdf', $this->session->uuid))->assertStatus(403);

        $this->session->update(['status' => 'approved_graded', 'approved_at' => now()]);

        $this->get(route('mahasiswa.instrumen.pdf', $this->session->uuid))->assertStatus(200);
    }

    public function test_new_care_session_starts_with_an_empty_assessment(): void
    {
        $this->actingAs($this->mahasiswa);

        $this->post(route('mahasiswa.store'), [
            'course_id'         => $this->course->id,
            'mentor_dosen_id'   => $this->dosen->id,
            'patient_name'      => 'Ny. Sartika',
            'medical_record_no' => 'RM-EMPTY-001',
            'age'               => 52,
            'gender'            => 'P',
            'triage_category'   => 'kuning',
        ]);

        $session = CareSession::where('medical_record_no', 'RM-EMPTY-001')->firstOrFail();

        // Tidak ada data dummy yang tersisa pada formulir pengkajian mahasiswa.
        $this->assertSame([], $session->assessment->assessment_payload);
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

    public function test_print_page_renders_vital_signs_without_error(): void
    {
        // `recorded_at` adalah kolom TIME sehingga bernilai string, bukan Carbon;
        // lembar cetak harus tetap merender jam dan SpO2 kosong dengan aman.
        VitalSignMonitoring::create([
            'care_session_id'  => $this->session->id,
            'recorded_at'      => '14:30',
            'blood_pressure'   => '130/85',
            'heart_rate'       => '88',
            'respiratory_rate' => '22',
            'temperature'      => '37.2',
            'spo2'             => '96',
            'gcs_score'        => '15',
        ]);

        VitalSignMonitoring::create([
            'care_session_id'  => $this->session->id,
            'recorded_at'      => '15:00',
            'blood_pressure'   => '125/80',
            'heart_rate'       => '84',
            'respiratory_rate' => '20',
            'temperature'      => '36.9',
            'spo2'             => '',
            'gcs_score'        => '15',
        ]);

        $this->actingAs($this->dosen);

        $response = $this->get(route('print.case', $this->session->uuid));

        $response->assertStatus(200);
        $response->assertSee('14:30');   // jam-menit, tanpa detik
        $response->assertSee('130/85');
        $response->assertSee('96%');     // kolom SpO2 memakai kolom `spo2`
        $response->assertDontSee('14:30:00');
    }

    public function test_admin_can_access_admin_dashboard_and_courses(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page->component('Admin/Dashboard')->has('courses'));

        $coursesResponse = $this->get(route('admin.courses'));
        $coursesResponse->assertStatus(200);
        $coursesResponse->assertInertia(fn (Assert $page) => $page->component('Admin/Courses')->has('courses'));
    }
}
