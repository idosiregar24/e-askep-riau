<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CareProcedureLog;
use App\Models\CareSession;
use App\Models\SessionReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DosenReviewController extends Controller
{
    public function show(string $uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $session = CareSession::with([
            'student',
            'course',
            'mentor',
            'assessment',
            'carePlans.sdki',
            'carePlans.slki',
            'carePlans.siki',
            'procedureLogs.procedure',
            'vitalSigns',
            'evaluationsAndHandovers',
            'review.dosen',
        ])->where('uuid', $uuid)->firstOrFail();

        // Ensure authorization: must be the assigned mentor or admin
        if (! $user->isAdmin() && $session->mentor_dosen_id !== $user->id) {
            abort(403, 'Anda tidak memiliki wewenang untuk menelaah berkas kasus mahasiswa ini.');
        }

        return \Inertia\Inertia::render('Dosen/Review', [
            'session' => $session,
        ]);
    }

    public function batchVerifySpo(Request $request, string $uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $session = CareSession::where('uuid', $uuid)->firstOrFail();

        if (! $user->isAdmin() && $session->mentor_dosen_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'procedure_ids'   => ['required', 'array'],
            'procedure_ids.*' => ['integer', 'exists:care_procedure_logs,id'],
        ]);

        CareProcedureLog::where('care_session_id', $session->id)
            ->whereIn('id', $validated['procedure_ids'])
            ->update([
                'is_verified'    => true,
                'verified_by_id' => $user->id,
                'verified_at'    => now(),
            ]);

        return back()->with('success', count($validated['procedure_ids']) . ' tindakan SPO berhasil diverifikasi dan dibubuhi e-paraf.');
    }

    public function requestRevision(Request $request, string $uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $session = CareSession::where('uuid', $uuid)->firstOrFail();

        if (! $user->isAdmin() && $session->mentor_dosen_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'revision_notes' => ['required', 'string', 'min:5'],
        ]);

        SessionReview::updateOrCreate(
            ['care_session_id' => $session->id],
            [
                'dosen_id'       => $user->id,
                'revision_notes' => $validated['revision_notes'],
                'reviewed_at'    => now(),
            ]
        );

        $session->update(['status' => 'need_revision']);

        return redirect()->route('dosen.dashboard')
            ->with('warning', "Berkas kasus pasien {$session->patient_name} dikembalikan ke mahasiswa untuk revisi.");
    }

    public function approveAndGrade(Request $request, string $uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $session = CareSession::where('uuid', $uuid)->firstOrFail();

        if (! $user->isAdmin() && $session->mentor_dosen_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'score_pengkajian' => ['required', 'numeric', 'min:0', 'max:100'],
            'score_diagnosa'   => ['required', 'numeric', 'min:0', 'max:100'],
            'score_prosedur'   => ['required', 'numeric', 'min:0', 'max:100'],
            'score_evaluasi'   => ['required', 'numeric', 'min:0', 'max:100'],
            'general_notes'    => ['nullable', 'string'],
        ]);

        // Calculate weighted final score (Sub-CPMK Clinical Rubric)
        // 25% Pengkajian, 25% Diagnosa & Rencana 3S, 30% Keterampilan SPO, 20% Evaluasi & SBAR
        $finalScore = (
            ($validated['score_pengkajian'] * 0.25) +
            ($validated['score_diagnosa'] * 0.25) +
            ($validated['score_prosedur'] * 0.30) +
            ($validated['score_evaluasi'] * 0.20)
        );

        $rubricScores = [
            'pengkajian' => $validated['score_pengkajian'],
            'diagnosa'   => $validated['score_diagnosa'],
            'prosedur'   => $validated['score_prosedur'],
            'evaluasi'   => $validated['score_evaluasi'],
        ];

        SessionReview::updateOrCreate(
            ['care_session_id' => $session->id],
            [
                'dosen_id'               => $user->id,
                'revision_notes'         => $validated['general_notes'] ?? 'Asuhan Keperawatan telah ditelaah, diverifikasi, dan disetujui.',
                'rubric_scores'          => $rubricScores,
                'final_score'            => round($finalScore, 2),
                'signature_snapshot_url' => $user->signature_path ?? '/signatures/dosen_default.png',
                'reviewed_at'            => now(),
            ]
        );

        $session->update([
            'status'      => 'approved_graded',
            'approved_at' => now(),
        ]);

        return redirect()->route('dosen.dashboard')
            ->with('success', "Berkas kasus pasien {$session->patient_name} berhasil disetujui dan dinilai (Skor Akhir: " . number_format($finalScore, 1) . "). Berkas kini terkunci permanen.");
    }
}
