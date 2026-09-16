<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CareProcedureLog;
use App\Models\CareSession;
use App\Models\SessionReview;
use App\Support\PenilaianInstrument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DosenReviewController extends Controller
{
    public function show(string $uuid)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $session = CareSession::with([
            'student.studentGroups',
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

        $stageType  = PenilaianInstrument::resolveStageType($session);
        $definition = PenilaianInstrument::for($stageType);
        $stored     = $session->review?->rubric_scores['instrument'] ?? [];

        return \Inertia\Inertia::render('Dosen/Review', [
            'session' => $session,

            // Instrumen penilaian klinik resmi yang mengisi formulir rubrik secara otomatis.
            'instrument' => [
                'stage_type'     => $definition['stage_type'],
                'stage_label'    => $definition['stage_label'],
                'title'          => $definition['title'],
                'subtitle'       => $definition['subtitle'],
                'faculty'        => $definition['faculty'],
                'items'          => $definition['items'],
                'max_item_score' => PenilaianInstrument::MAX_ITEM_SCORE,
                'max_score'      => PenilaianInstrument::maxScore($definition),
                'passing_score'  => PenilaianInstrument::PASSING_SCORE,
                'score_legend'   => PenilaianInstrument::SCORE_LEGEND,
                'rubric_labels'  => PenilaianInstrument::RUBRIC_LABELS,
                'rubric_weights' => PenilaianInstrument::RUBRIC_WEIGHTS,
                'identity'       => PenilaianInstrument::identityValues($definition, $session, $stored['identity'] ?? []),
                'saved_scores'   => (object) ($stored['item_scores'] ?? []),
                'saved_notes'    => (object) ($stored['item_notes'] ?? []),
            ],
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

        $session->loadMissing(['student.studentGroups', 'course', 'mentor', 'assessment', 'carePlans.sdki']);

        $definition = PenilaianInstrument::for(PenilaianInstrument::resolveStageType($session));
        $itemCount  = count($definition['items']);

        $validated = $request->validate([
            'instrument_scores'   => ['required', 'array', 'size:' . $itemCount],
            'instrument_scores.*' => ['required', 'integer', 'min:0', 'max:' . PenilaianInstrument::MAX_ITEM_SCORE],
            'instrument_notes'    => ['nullable', 'array'],
            'instrument_notes.*'  => ['nullable', 'string', 'max:500'],
            'identity'            => ['nullable', 'array'],
            'identity.*'          => ['nullable', 'string', 'max:255'],
            'general_notes'       => ['nullable', 'string', 'max:2000'],
        ], [
            'instrument_scores.required' => 'Instrumen penilaian klinik wajib diisi sebelum berkas disahkan.',
            'instrument_scores.size'     => "Seluruh {$itemCount} aspek instrumen penilaian wajib diberi skor.",
            'instrument_scores.*.max'    => 'Skor tiap aspek instrumen maksimal ' . PenilaianInstrument::MAX_ITEM_SCORE . '.',
        ]);

        // Nilai rubrik Sub-CPMK diturunkan dari instrumen agar konsisten dengan lembar fisik.
        $summary    = PenilaianInstrument::summarize($definition, $validated['instrument_scores']);
        $finalScore = PenilaianInstrument::weightedFinalScore($summary['rubric']);

        $rubricScores = $summary['rubric'] + [
            'instrument' => [
                'stage_type'  => $definition['stage_type'],
                'item_scores' => $validated['instrument_scores'],
                'item_notes'  => array_filter($validated['instrument_notes'] ?? [], fn ($note) => filled($note)),
                'identity'    => array_filter($validated['identity'] ?? [], fn ($value) => filled($value)),
                'total_score' => $summary['total_score'],
                'max_score'   => $summary['max_score'],
                'nilai_akhir' => $summary['nilai_akhir'],
                'kategori'    => $summary['kategori'],
            ],
        ];

        // E-paraf dan pengesahan nilai wajib atomik (lihat security-standards.md).
        DB::transaction(function () use ($session, $user, $validated, $rubricScores, $finalScore) {
            SessionReview::updateOrCreate(
                ['care_session_id' => $session->id],
                [
                    'dosen_id'               => $user->id,
                    'revision_notes'         => $validated['general_notes'] ?? 'Asuhan Keperawatan telah ditelaah, diverifikasi, dan disetujui.',
                    'rubric_scores'          => $rubricScores,
                    'final_score'            => $finalScore,
                    'signature_snapshot_url' => $user->signature_path ?? '/signatures/dosen_default.png',
                    'reviewed_at'            => now(),
                ]
            );

            $session->update([
                'status'      => 'approved_graded',
                'approved_at' => now(),
            ]);
        });

        return redirect()->route('dosen.dashboard')->with(
            'success',
            "Berkas kasus pasien {$session->patient_name} berhasil disetujui dan dinilai. "
            . "Instrumen {$definition['stage_label']}: {$summary['total_score']}/{$summary['max_score']} "
            . '(Nilai ' . number_format($summary['nilai_akhir'], 1) . " — {$summary['kategori']}), "
            . 'Skor Akhir Portofolio ' . number_format($finalScore, 1) . '. Berkas kini terkunci permanen.'
        );
    }
}
