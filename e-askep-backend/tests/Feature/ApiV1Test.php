<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_api_health_check(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'app'    => 'e-Askep Poltekkes Kemenkes Riau API',
            ]);
    }

    public function test_mahasiswa_can_login_and_access_protected_routes(): void
    {
        // 1. Test Login
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'login'    => 'mahasiswa@poltekkes-riau.ac.id',
            'password' => 'mhs123',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'nim_nip', 'role'],
                ],
            ]);

        $token = $loginResponse->json('data.token');

        // 2. Test Profile /me
        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(200)
            ->assertJsonPath('data.role', 'mahasiswa');

        // 3. Test Master Courses
        $coursesResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/master/courses');

        $coursesResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        // 4. Test Inisiasi Sesi Asuhan Baru
        $course = Course::first();
        $dosen = User::where('role', 'dosen')->first();

        $sessionResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/sessions', [
                'course_id'          => $course->id,
                'mentor_dosen_id'    => $dosen->id,
                'patient_name'       => 'Ny. Rahmawati',
                'medical_record_no'  => 'RM-2026-0091',
                'age'                => '45 tahun',
                'gender'             => 'P',
                'triage_category'    => 'kuning',
                'stage_type'         => 'kgd',
            ]);

        $sessionResponse->assertStatus(201)
            ->assertJsonPath('data.patient_name', 'Ny. Rahmawati')
            ->assertJsonPath('data.status', 'draft');

        $sessionId = $sessionResponse->json('data.id');

        // 5. Test Simpan Draf Pengkajian
        $draftResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/v1/sessions/{$sessionId}/draft", [
                'triage_category'    => 'kuning',
                'assessment_payload' => [
                    'primary_survey' => [
                        'airway'    => 'Paten',
                        'breathing' => ['rr' => 24, 'spo2' => 97],
                    ],
                ],
            ]);

        $draftResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        // 6. Test Submit Sesi ke Dosen
        $submitResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/sessions/{$sessionId}/submit");

        $submitResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'submitted');
    }

    public function test_dosen_can_review_and_grade_session(): void
    {
        // 1. Dosen Login
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'login'    => 'dosen@poltekkes-riau.ac.id',
            'password' => 'dosen123',
        ]);

        $token = $loginResponse->json('data.token');

        // 2. Dosen View Submissions
        $submissionsResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/ci/submissions');

        $submissionsResponse->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
