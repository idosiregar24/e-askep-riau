<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\MasterSdki;
use App\Models\MasterSiki;
use App\Models\MasterSlki;
use App\Models\MasterSpoProcedure;
use App\Models\StudentGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $dosen;
    protected User $mahasiswa;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        $this->dosen = User::where('role', 'dosen')->first();
        $this->mahasiswa = User::where('role', 'mahasiswa')->first();
        $this->course = Course::first();
    }

    // 1. USER CRUD
    public function test_admin_can_view_users_page(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.users'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page->component('Admin/Users')->has('users'));
    }

    public function test_admin_can_create_user(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.users.store'), [
            'name'         => 'Ns. Siti Rahma, M.Kep',
            'nim_nip'      => '198501012010122001',
            'email'        => 'siti.rahma@poltekkes-riau.ac.id',
            'role'         => 'dosen',
            'password'     => 'password123',
            'phone_number' => '081234567890',
            'is_active'    => true,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'email'   => 'siti.rahma@poltekkes-riau.ac.id',
            'nim_nip' => '198501012010122001',
            'role'    => 'dosen',
        ]);
    }

    public function test_admin_can_update_user(): void
    {
        $this->actingAs($this->admin);

        $targetUser = User::where('role', 'mahasiswa')->first();

        $response = $this->put(route('admin.users.update', $targetUser->id), [
            'name'         => 'Mahasiswa Updated Name',
            'nim_nip'      => $targetUser->nim_nip,
            'email'        => $targetUser->email,
            'role'         => 'mahasiswa',
            'password'     => '', // leave unchanged
            'phone_number' => '08999999999',
            'is_active'    => false,
        ]);

        $response->assertSessionHas('success');
        $targetUser->refresh();
        $this->assertEquals('Mahasiswa Updated Name', $targetUser->name);
        $this->assertEquals(0, $targetUser->is_active);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $this->actingAs($this->admin);

        $response = $this->delete(route('admin.users.destroy', $this->admin->id));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_admin_can_delete_other_user(): void
    {
        $this->actingAs($this->admin);

        $newUser = User::create([
            'name'      => 'Delete Me',
            'nim_nip'   => 'DEL999',
            'email'     => 'del@test.com',
            'role'      => 'mahasiswa',
            'password'  => bcrypt('secret123'),
            'is_active' => true,
        ]);

        $response = $this->delete(route('admin.users.destroy', $newUser->id));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $newUser->id]);
    }

    // 2. COURSE CRUD
    public function test_admin_can_create_course(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.courses.store'), [
            'code'          => 'WAT5.99.26',
            'name'          => 'Keperawatan Jiwa & Komunitas',
            'program_study' => 'D-III Keperawatan',
            'academic_year' => '2025/2026 Genap',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('courses', [
            'code' => 'WAT5.99.26',
        ]);
    }

    public function test_admin_can_update_course(): void
    {
        $this->actingAs($this->admin);

        $response = $this->put(route('admin.courses.update', $this->course->id), [
            'code'          => $this->course->code,
            'name'          => 'Updated Course Name',
            'program_study' => 'Sarjana Terapan Keperawatan',
            'academic_year' => '2025/2026 Ganjil',
        ]);

        $response->assertSessionHas('success');
        $this->course->refresh();
        $this->assertEquals('Updated Course Name', $this->course->name);
    }

    public function test_admin_can_delete_course(): void
    {
        $this->actingAs($this->admin);

        $course = Course::create([
            'code'          => 'DEL.COURSE',
            'name'          => 'Course to Delete',
            'program_study' => 'D-III Keperawatan',
            'academic_year' => '2025/2026',
        ]);

        $response = $this->delete(route('admin.courses.destroy', $course->id));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    // 3. GROUPS CRUD
    public function test_admin_can_create_group(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.groups.store'), [
            'course_id'       => $this->course->id,
            'mentor_dosen_id' => $this->dosen->id,
            'student_id'      => $this->mahasiswa->id,
            'group_name'      => 'Kelompok Khusus IGD Siaga',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('student_groups', [
            'group_name' => 'Kelompok Khusus IGD Siaga',
        ]);
    }

    public function test_admin_can_update_group(): void
    {
        $this->actingAs($this->admin);

        $group = StudentGroup::create([
            'course_id'       => $this->course->id,
            'mentor_dosen_id' => $this->dosen->id,
            'student_id'      => $this->mahasiswa->id,
            'group_name'      => 'Old Group Name',
        ]);

        $response = $this->put(route('admin.groups.update', $group->id), [
            'course_id'       => $this->course->id,
            'mentor_dosen_id' => $this->dosen->id,
            'student_id'      => $this->mahasiswa->id,
            'group_name'      => 'New Group Name',
        ]);

        $response->assertSessionHas('success');
        $group->refresh();
        $this->assertEquals('New Group Name', $group->group_name);
    }

    public function test_admin_can_delete_group(): void
    {
        $this->actingAs($this->admin);

        $group = StudentGroup::create([
            'course_id'       => $this->course->id,
            'mentor_dosen_id' => $this->dosen->id,
            'student_id'      => $this->mahasiswa->id,
            'group_name'      => 'Group To Delete',
        ]);

        $response = $this->delete(route('admin.groups.destroy', $group->id));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('student_groups', ['id' => $group->id]);
    }

    // 4. MASTER 3S CRUD (SDKI, SLKI, SIKI)
    public function test_admin_can_view_master_3s_with_tabs(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.master3s', ['tab' => 'sdki']));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Master3s')
            ->has('sdkiList')
            ->has('slkiList')
            ->has('sikiList')
            ->has('sdkiCount')
            ->has('slkiCount')
            ->has('sikiCount')
        );
    }

    public function test_admin_can_crud_sdki(): void
    {
        $this->actingAs($this->admin);

        // Store
        $storeResp = $this->post(route('admin.sdki.store'), [
            'code'         => 'D.9999',
            'title'        => 'Diagnosa Uji Klinis Baru',
            'category'     => 'Fisiologis',
            'sub_category' => 'Respirasi',
        ]);
        $storeResp->assertSessionHas('success');
        $sdki = MasterSdki::where('code', 'D.9999')->first();
        $this->assertNotNull($sdki);

        // Update
        $updateResp = $this->put(route('admin.sdki.update', $sdki->id), [
            'code'         => 'D.9999',
            'title'        => 'Diagnosa Uji Klinis Diperbarui',
            'category'     => 'Psikologis',
            'sub_category' => 'Integritas Ego',
        ]);
        $updateResp->assertSessionHas('success');
        $sdki->refresh();
        $this->assertEquals('Diagnosa Uji Klinis Diperbarui', $sdki->title);

        // Delete
        $delResp = $this->delete(route('admin.sdki.destroy', $sdki->id));
        $delResp->assertSessionHas('success');
        $this->assertDatabaseMissing('master_sdki', ['id' => $sdki->id]);
    }

    public function test_admin_can_crud_slki(): void
    {
        $this->actingAs($this->admin);

        // Store
        $storeResp = $this->post(route('admin.slki.store'), [
            'code'  => 'L.99999',
            'title' => 'Luaran Uji Klinis Baru',
        ]);
        $storeResp->assertSessionHas('success');
        $slki = MasterSlki::where('code', 'L.99999')->first();
        $this->assertNotNull($slki);

        // Update
        $updateResp = $this->put(route('admin.slki.update', $slki->id), [
            'code'  => 'L.99999',
            'title' => 'Luaran Uji Klinis Diperbarui',
        ]);
        $updateResp->assertSessionHas('success');
        $slki->refresh();
        $this->assertEquals('Luaran Uji Klinis Diperbarui', $slki->title);

        // Delete
        $delResp = $this->delete(route('admin.slki.destroy', $slki->id));
        $delResp->assertSessionHas('success');
        $this->assertDatabaseMissing('master_slki', ['id' => $slki->id]);
    }

    public function test_admin_can_crud_siki(): void
    {
        $this->actingAs($this->admin);

        // Store
        $storeResp = $this->post(route('admin.siki.store'), [
            'code'  => 'I.99999',
            'title' => 'Intervensi Uji Klinis Baru',
        ]);
        $storeResp->assertSessionHas('success');
        $siki = MasterSiki::where('code', 'I.99999')->first();
        $this->assertNotNull($siki);

        // Update
        $updateResp = $this->put(route('admin.siki.update', $siki->id), [
            'code'  => 'I.99999',
            'title' => 'Intervensi Uji Klinis Diperbarui',
        ]);
        $updateResp->assertSessionHas('success');
        $siki->refresh();
        $this->assertEquals('Intervensi Uji Klinis Diperbarui', $siki->title);

        // Delete
        $delResp = $this->delete(route('admin.siki.destroy', $siki->id));
        $delResp->assertSessionHas('success');
        $this->assertDatabaseMissing('master_siki', ['id' => $siki->id]);
    }

    // 5. MASTER SPO CRUD
    public function test_admin_can_crud_spo(): void
    {
        $this->actingAs($this->admin);

        // Store
        $storeResp = $this->post(route('admin.spo.store'), [
            'course_id'          => $this->course->id,
            'sub_cpmk_reference' => 'Sub-CPMK 5',
            'domain_category'    => 'Respirasi Akut',
            'procedure_name'     => 'Prosedur Uji Tindakan SPO',
        ]);
        $storeResp->assertSessionHas('success');
        $spo = MasterSpoProcedure::where('procedure_name', 'Prosedur Uji Tindakan SPO')->first();
        $this->assertNotNull($spo);

        // Update
        $updateResp = $this->put(route('admin.spo.update', $spo->id), [
            'course_id'          => $this->course->id,
            'sub_cpmk_reference' => 'Sub-CPMK 6',
            'domain_category'    => 'Respirasi Lanjut',
            'procedure_name'     => 'Prosedur Uji Tindakan SPO (Updated)',
        ]);
        $updateResp->assertSessionHas('success');
        $spo->refresh();
        $this->assertEquals('Prosedur Uji Tindakan SPO (Updated)', $spo->procedure_name);

        // Delete
        $delResp = $this->delete(route('admin.spo.destroy', $spo->id));
        $delResp->assertSessionHas('success');
        $this->assertDatabaseMissing('master_spo_procedures', ['id' => $spo->id]);
    }
}
