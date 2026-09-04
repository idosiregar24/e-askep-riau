<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CareSession;
use App\Models\CareSessionAssessment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CareSessionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = CareSession::with(['course', 'mentor', 'assessment']);

        if ($user->role === 'mahasiswa') {
            $query->where('student_id', $user->id);
        } elseif ($user->role === 'dosen') {
            $query->where('mentor_dosen_id', $user->id);
        }

        $sessions = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Daftar sesi asuhan keperawatan.',
            'data'    => $sessions->items(),
            'meta'    => [
                'current_page' => $sessions->currentPage(),
                'total'        => $sessions->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'course_id'          => ['required', 'exists:courses,id'],
            'mentor_dosen_id'    => ['required', 'exists:users,id'],
            'patient_name'       => ['required', 'string', 'max:255'],
            'medical_record_no'  => ['nullable', 'string', 'max:100'],
            'age'                => ['required', 'string', 'max:50'],
            'gender'             => ['required', 'in:L,P'],
            'triage_category'    => ['nullable', 'in:merah,kuning,hijau,hitam'],
            'stage_type'         => ['required', 'in:kgd,kdm,kmb'],
        ]);

        $session = CareSession::create([
            'uuid'              => (string) Str::uuid(),
            'student_id'        => $user->id,
            'course_id'         => $validated['course_id'],
            'mentor_dosen_id'   => $validated['mentor_dosen_id'],
            'patient_name'      => $validated['patient_name'],
            'medical_record_no' => $validated['medical_record_no'] ?? null,
            'age'               => $validated['age'],
            'gender'            => $validated['gender'],
            'triage_category'   => $validated['triage_category'] ?? null,
            'status'            => 'draft',
        ]);

        // Inisiasi data pengkajian awal
        CareSessionAssessment::create([
            'care_session_id'    => $session->id,
            'stage_type'         => $validated['stage_type'],
            'assessment_payload' => [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sesi asuhan berhasil diinisiasi.',
            'data'    => $session->load(['course', 'mentor', 'assessment']),
        ], 201);
    }

    public function show(CareSession $session): JsonResponse
    {
        $session->load([
            'course',
            'mentor',
            'student',
            'assessment',
            'carePlans.sdki',
            'carePlans.slki',
            'carePlans.siki',
            'procedureLogs.procedure',
            'vitalSigns',
            'evaluationsAndHandovers',
            'review',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Detail sesi asuhan klinis.',
            'data'    => $session,
        ]);
    }

    public function saveDraft(Request $request, CareSession $session): JsonResponse
    {
        $user = $request->user();

        if ($user->id !== $session->student_id || ! in_array($session->status, ['draft', 'need_revision'])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses untuk menyunting draf ini.',
            ], 403);
        }

        $validated = $request->validate([
            'triage_category'    => ['nullable', 'in:merah,kuning,hijau,hitam'],
            'assessment_payload' => ['required', 'array'],
        ]);

        if (isset($validated['triage_category'])) {
            $session->update(['triage_category' => $validated['triage_category']]);
        }

        $session->assessment()->updateOrCreate(
            ['care_session_id' => $session->id],
            [
                'stage_type'         => $session->assessment?->stage_type ?? 'kgd',
                'assessment_payload' => $validated['assessment_payload'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Draf pengkajian berhasil disimpan.',
            'data'    => $session->fresh('assessment'),
        ]);
    }

    public function submit(Request $request, CareSession $session): JsonResponse
    {
        $user = $request->user();

        if ($user->id !== $session->student_id || ! in_array($session->status, ['draft', 'need_revision'])) {
            return response()->json([
                'success' => false,
                'message' => 'Berkas tidak dapat disubmit pada status ini.',
            ], 403);
        }

        $session->update([
            'status'       => 'submitted',
            'submitted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berkas asuhan berhasil disubmit ke Dosen Pembimbing.',
            'data'    => $session->fresh(),
        ]);
    }
}
