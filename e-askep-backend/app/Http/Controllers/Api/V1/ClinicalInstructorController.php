<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CareProcedureLog;
use App\Models\CareSession;
use App\Models\SessionReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClinicalInstructorController extends Controller
{
    public function submissions(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'dosen' && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $query = CareSession::with(['student', 'course', 'assessment', 'review'])
            ->where('mentor_dosen_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $submissions = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Antrean telaah kasus mahasiswa bimbingan.',
            'data'    => $submissions->items(),
            'meta'    => [
                'total' => $submissions->total(),
            ],
        ]);
    }

    public function verifyProcedures(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'care_session_id'     => ['required', 'exists:care_sessions,id'],
            'procedure_log_ids'   => ['required', 'array'],
            'procedure_log_ids.*' => ['exists:care_procedure_logs,id'],
            'notes'               => ['nullable', 'string'],
        ]);

        $session = CareSession::findOrFail($validated['care_session_id']);

        if ($user->role !== 'dosen' || $session->mentor_dosen_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Akses tidak sah.'], 403);
        }

        DB::transaction(function () use ($validated, $user) {
            CareProcedureLog::whereIn('id', $validated['procedure_log_ids'])
                ->where('care_session_id', $validated['care_session_id'])
                ->update([
                    'is_verified'    => true,
                    'verified_by_id' => $user->id,
                    'verified_at'    => now(),
                    'notes'          => $validated['notes'] ?? null,
                ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Tindakan berhasil diverifikasi dengan E-Paraf Dosen/CI.',
        ]);
    }

    public function requestRevision(Request $request, CareSession $session): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'dosen' || $session->mentor_dosen_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Akses tidak sah.'], 403);
        }

        $validated = $request->validate([
            'revision_notes' => ['required', 'string'],
        ]);

        $session->update(['status' => 'need_revision']);

        SessionReview::updateOrCreate(
            ['care_session_id' => $session->id],
            [
                'dosen_id'       => $user->id,
                'revision_notes' => $validated['revision_notes'],
                'reviewed_at'    => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Catatan revisi berhasil dikirim ke mahasiswa.',
            'data'    => $session->fresh('review'),
        ]);
    }

    public function gradeSession(Request $request, CareSession $session): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'dosen' || $session->mentor_dosen_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Akses tidak sah.'], 403);
        }

        $validated = $request->validate([
            'rubric_scores'          => ['required', 'array'],
            'final_score'            => ['required', 'numeric', 'min:0', 'max:100'],
            'signature_snapshot_url' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($session, $user, $validated) {
            SessionReview::updateOrCreate(
                ['care_session_id' => $session->id],
                [
                    'dosen_id'               => $user->id,
                    'rubric_scores'          => $validated['rubric_scores'],
                    'final_score'            => $validated['final_score'],
                    'signature_snapshot_url' => $validated['signature_snapshot_url'] ?? null,
                    'reviewed_at'            => now(),
                ]
            );

            $session->update([
                'status'      => 'approved_graded',
                'approved_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Penilaian rubrik berhasil disimpan dan berkas asuhan terkunci resmi.',
            'data'    => $session->fresh(['review', 'assessment']),
        ]);
    }
}
