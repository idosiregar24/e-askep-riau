<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CareSession;
use App\Services\InstrumentDocxWriter;
use App\Support\PenilaianInstrument;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Ekspor Instrumen Penilaian Pengkajian Proses Keperawatan (KDM/KGD/KMB)
 * ke berkas PDF dan Word dengan tata letak resmi Poltekkes Kemenkes Riau.
 */
class InstrumentExportController extends Controller
{
    public function __construct(private readonly InstrumentDocxWriter $docxWriter)
    {
    }

    /** Unduh instrumen penilaian dalam format PDF (A4 potret). */
    public function pdf(Request $request, string $uuid): Response
    {
        $context = $this->buildContext($request, $uuid);

        $pdf = Pdf::loadView('print.instrumen-penilaian', $context)
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', false);

        return $pdf->download(
            $this->docxWriter->filename($context['definition'], $context['session']->student?->name ?? 'Mahasiswa', 'pdf')
        );
    }

    /** Unduh instrumen penilaian dalam format Word (.docx). */
    public function word(Request $request, string $uuid): Response
    {
        $context = $this->buildContext($request, $uuid);

        $binary = $this->docxWriter->render(
            $context['definition'],
            $context['summary'],
            $context['identity'],
            $context['notes'],
            $context['footer'] + ['general_notes' => $context['generalNotes']]
        );

        $filename = $this->docxWriter->filename(
            $context['definition'],
            $context['session']->student?->name ?? 'Mahasiswa'
        );

        return response($binary, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length'      => (string) strlen($binary),
        ]);
    }

    /**
     * Susun seluruh data yang dibutuhkan kedua renderer.
     *
     * Skor diambil dari input permintaan bila dosen mengekspor pratinjau sebelum
     * menyimpan; jika tidak ada, dipakai skor instrumen yang sudah tersimpan pada
     * berkas telaah (`session_reviews.rubric_scores`).
     */
    private function buildContext(Request $request, string $uuid): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $session = CareSession::with([
            'student.studentGroups',
            'course',
            'mentor',
            'assessment',
            'carePlans.sdki',
            'review.dosen',
        ])->where('uuid', $uuid)->firstOrFail();

        // Instrumen penilaian hanya boleh diunduh oleh pembimbing penilai, admin,
        // atau mahasiswa pemilik berkas setelah berkas disahkan.
        $isOwner = $session->student_id === $user->id;
        $canView = $user->isAdmin()
            || $session->mentor_dosen_id === $user->id
            || ($isOwner && in_array($session->status, ['approved_graded', 'archived'], true));

        abort_unless($canView, 403, 'Anda tidak berwenang mengunduh instrumen penilaian berkas ini.');

        $stageType  = PenilaianInstrument::resolveStageType($session);
        $definition = PenilaianInstrument::for($stageType);

        $stored = $session->review?->rubric_scores['instrument'] ?? [];

        // Dosen dapat mengirim skor tak tersimpan lewat query string untuk pratinjau.
        $itemScores = $this->scoresFromRequest($request) ?? ($stored['item_scores'] ?? []);
        $notes      = $this->notesFromRequest($request) ?? ($stored['item_notes'] ?? []);

        $summary  = PenilaianInstrument::summarize($definition, $itemScores);
        $identity = PenilaianInstrument::identityValues($definition, $session, $stored['identity'] ?? []);

        $reviewer = $session->review?->dosen ?? $session->mentor;

        return [
            'session'      => $session,
            'definition'   => $definition,
            'summary'      => $summary,
            'identity'     => $identity,
            'notes'        => $notes,
            'scoreLegend'  => PenilaianInstrument::SCORE_LEGEND,
            'passingScore' => PenilaianInstrument::PASSING_SCORE,
            'generalNotes' => $session->review?->revision_notes ?? '',
            'footer'       => [
                'place'          => 'Pekanbaru',
                'date'           => optional($session->review?->reviewed_at ?? now())->translatedFormat('d F Y'),
                'assessor'       => $reviewer?->name ?? '',
                'assessor_id'    => $reviewer?->nim_nip ?? '',
                'signature_path' => $this->signatureAbsolutePath($session),
            ],
        ];
    }

    /**
     * Skor aspek dari query string (`scores[1]=4&scores[2]=3`).
     *
     * @return array<int, int>|null  Null bila permintaan tidak membawa skor sama sekali.
     */
    private function scoresFromRequest(Request $request): ?array
    {
        $raw = $request->input('scores');
        if (! is_array($raw)) {
            return null;
        }

        $scores = [];
        foreach ($raw as $no => $value) {
            $scores[(int) $no] = PenilaianInstrument::normalizeScore($value);
        }

        return $scores;
    }

    /**
     * Catatan per aspek dari query string (`notes[1]=...`).
     *
     * @return array<int, string>|null
     */
    private function notesFromRequest(Request $request): ?array
    {
        $raw = $request->input('notes');
        if (! is_array($raw)) {
            return null;
        }

        $notes = [];
        foreach ($raw as $no => $value) {
            $notes[(int) $no] = mb_substr(trim((string) $value), 0, 500);
        }

        return $notes;
    }

    /**
     * Path lokal berkas paraf digital penilai untuk disematkan pada PDF.
     * Dompdf berjalan tanpa akses jaringan, jadi hanya berkas lokal yang dipakai.
     */
    private function signatureAbsolutePath(CareSession $session): ?string
    {
        $relative = $session->review?->signature_snapshot_url
            ?? $session->review?->dosen?->signature_path
            ?? $session->mentor?->signature_path;

        if (! $relative) {
            return null;
        }

        $path = public_path(ltrim(parse_url($relative, PHP_URL_PATH) ?? $relative, '/'));

        return is_file($path) ? $path : null;
    }
}
