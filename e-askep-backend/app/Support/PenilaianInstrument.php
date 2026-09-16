<?php

namespace App\Support;

use App\Models\CareSession;

/**
 * Definisi baku Instrumen Penilaian Pengkajian Proses Keperawatan
 * Jurusan Keperawatan Poltekkes Kemenkes Riau (stase KDM, KGD, KMB).
 *
 * Sumber acuan fisik:
 *   .claude/plan/Instrumen_Penilaian_Pengkajian_KDM.docx
 *   .claude/plan/Instrumen_Penilaian_Pengkajian_KGD.docx
 *   .claude/plan/Instrumen_Penilaian_Pengkajian_KMB.docx
 *
 * Kelas ini adalah satu-satunya sumber kebenaran struktur instrumen: dipakai
 * bersama oleh formulir telaah dosen (React), renderer PDF, dan penulis DOCX,
 * sehingga tata letak cetak selalu identik 1:1 dengan instrumen fisik.
 */
class PenilaianInstrument
{
    /** Skor tiap aspek pada instrumen fisik berskala 0-4. */
    public const MAX_ITEM_SCORE = 4;

    /** Ambang batas kelulusan klinis (Kompeten) sesuai instrumen. */
    public const PASSING_SCORE = 75;

    /**
     * Bobot rubrik Sub-CPMK yang dipakai untuk menghitung nilai akhir portofolio.
     * Setiap aspek instrumen dipetakan ke salah satu kelompok ini lewat kunci `group`.
     */
    public const RUBRIC_WEIGHTS = [
        'pengkajian' => 0.25,
        'diagnosa'   => 0.25,
        'prosedur'   => 0.30,
        'evaluasi'   => 0.20,
    ];

    public const RUBRIC_LABELS = [
        'pengkajian' => 'Pengkajian Klinis',
        'diagnosa'   => 'Analisa & Diagnosa 3S',
        'prosedur'   => 'Keterampilan Tindakan SPO',
        'evaluasi'   => 'Evaluasi & Serah Terima',
    ];

    /** Keterangan skor 0-4 yang dicetak di bawah tabel penilaian. */
    public const SCORE_LEGEND = [
        0 => 'Tidak dilakukan/tidak diisi',
        1 => 'Dilakukan sebagian kecil, banyak kesalahan',
        2 => 'Dilakukan cukup, ada kekurangan',
        3 => 'Dilakukan baik, sesuai prosedur',
        4 => 'Dilakukan lengkap, tepat, dan sistematis',
    ];

    /**
     * Definisi lengkap ketiga instrumen.
     *
     * `identity` = baris kop identitas 2 kolom pada instrumen fisik; kunci `source`
     * menyatakan field sesi asuhan yang otomatis mengisi kolom tersebut.
     */
    public static function definitions(): array
    {
        return [
            'kdm' => [
                'stage_type'    => 'kdm',
                'stage_label'   => 'KDM',
                'faculty'       => 'JURUSAN KEPERAWATAN - PROGRAM STUDI SARJANA TERAPAN KEPERAWATAN PEKANBARU',
                'title'         => 'INSTRUMEN PENILAIAN PENGKAJIAN PROSES KEPERAWATAN',
                'subtitle'      => 'Praktikum Kebutuhan Dasar Manusia (KDM) - Mengacu RPS WAT6.07.24',
                'identity'      => [
                    ['label' => 'Nama Mahasiswa',    'source' => 'student_name'],
                    ['label' => 'Umur/JK Klien',     'source' => 'patient_age_gender'],
                    ['label' => 'NIM/Kelompok',      'source' => 'student_nim_group'],
                    ['label' => 'Tanggal Praktikum', 'source' => 'practice_date'],
                    ['label' => 'Kebutuhan Dasar',   'source' => 'case_focus'],
                    ['label' => 'Penilai',           'source' => 'mentor_name'],
                ],
                'items' => [
                    ['no' => 1,  'group' => 'pengkajian', 'aspect' => 'Identitas pasien/klien simulasi dicatat lengkap'],
                    ['no' => 2,  'group' => 'pengkajian', 'aspect' => 'Riwayat kesehatan singkat (keluhan utama, RKS, RKD, alergi) tergali lengkap'],
                    ['no' => 3,  'group' => 'pengkajian', 'aspect' => 'Pengkajian kebutuhan dasar (anamnesis & pemeriksaan) sesuai fokus latihan'],
                    ['no' => 4,  'group' => 'prosedur',   'aspect' => 'Tindakan keperawatan pemenuhan kebutuhan dasar dilakukan sesuai SPO'],
                    ['no' => 5,  'group' => 'pengkajian', 'aspect' => 'Pemeriksaan fisik umum (head to toe ringkas) dilakukan sistematis'],
                    ['no' => 6,  'group' => 'diagnosa',   'aspect' => 'Analisa data (DS, DO, etiologi, masalah keperawatan) tepat dan logis'],
                    ['no' => 7,  'group' => 'diagnosa',   'aspect' => 'Diagnosa keperawatan dirumuskan sesuai SDKI'],
                    ['no' => 8,  'group' => 'diagnosa',   'aspect' => 'Rencana keperawatan (tujuan/kriteria hasil SLKI & intervensi SIKI) sesuai masalah'],
                    ['no' => 9,  'group' => 'prosedur',   'aspect' => 'Implementasi keperawatan dilakukan dan didokumentasikan dengan jelas'],
                    ['no' => 10, 'group' => 'evaluasi',   'aspect' => 'Evaluasi keperawatan disusun dalam format SOAP'],
                ],
            ],

            'kgd' => [
                'stage_type'    => 'kgd',
                'stage_label'   => 'KGD',
                'faculty'       => 'JURUSAN KEPERAWATAN - PROGRAM STUDI D-III KEPERAWATAN',
                'title'         => 'INSTRUMEN PENILAIAN PENGKAJIAN PROSES KEPERAWATAN GAWAT DARURAT',
                'subtitle'      => 'Praktikum Keperawatan Gawat Darurat (KGD)',
                'identity'      => [
                    ['label' => 'Nama Mahasiswa',    'source' => 'student_name'],
                    ['label' => 'Umur/JK Pasien',    'source' => 'patient_age_gender'],
                    ['label' => 'NIM',               'source' => 'student_nim'],
                    ['label' => 'Tanggal Praktikum', 'source' => 'practice_date'],
                    ['label' => 'Kasus/Kegawatan',   'source' => 'case_focus'],
                    ['label' => 'Penilai',           'source' => 'mentor_name'],
                ],
                'items' => [
                    ['no' => 1,  'group' => 'pengkajian', 'aspect' => 'Identitas pasien dan data awal kejadian dicatat lengkap dan akurat'],
                    ['no' => 2,  'group' => 'pengkajian', 'aspect' => 'Triase/klasifikasi kegawatan ditentukan dan didokumentasikan dengan tepat'],
                    ['no' => 3,  'group' => 'pengkajian', 'aspect' => 'Survei primer (Airway) dikaji dan didokumentasikan sesuai prosedur'],
                    ['no' => 4,  'group' => 'pengkajian', 'aspect' => 'Survei primer (Breathing) dikaji dan didokumentasikan sesuai prosedur'],
                    ['no' => 5,  'group' => 'pengkajian', 'aspect' => 'Survei primer (Circulation) dikaji dan didokumentasikan sesuai prosedur'],
                    ['no' => 6,  'group' => 'pengkajian', 'aspect' => 'Survei primer (Disability) dikaji dan didokumentasikan sesuai prosedur'],
                    ['no' => 7,  'group' => 'pengkajian', 'aspect' => 'Survei primer (Exposure/Environment) dikaji dan didokumentasikan sesuai prosedur'],
                    ['no' => 8,  'group' => 'pengkajian', 'aspect' => 'Survei sekunder - Riwayat AMPLE digali secara lengkap'],
                    ['no' => 9,  'group' => 'pengkajian', 'aspect' => 'Survei sekunder - Pemeriksaan fisik head to toe dilakukan sistematis'],
                    ['no' => 10, 'group' => 'prosedur',   'aspect' => 'Tindakan kegawatdaruratan dilakukan sesuai kompetensi dan didokumentasikan'],
                    ['no' => 11, 'group' => 'diagnosa',   'aspect' => 'Analisa data (DS, DO, etiologi, masalah keperawatan) tepat dan logis'],
                    ['no' => 12, 'group' => 'diagnosa',   'aspect' => 'Diagnosa keperawatan dirumuskan sesuai SDKI dan prioritas kegawatan'],
                    ['no' => 13, 'group' => 'diagnosa',   'aspect' => 'Rencana dan implementasi tindakan prioritas sesuai SLKI & SIKI'],
                    ['no' => 14, 'group' => 'evaluasi',   'aspect' => 'Evaluasi, monitoring, dan reassessment terdokumentasi berkala'],
                    ['no' => 15, 'group' => 'evaluasi',   'aspect' => 'Dokumentasi serah terima (SBAR) disusun jelas dan sistematis'],
                ],
            ],

            'kmb' => [
                'stage_type'    => 'kmb',
                'stage_label'   => 'KMB',
                'faculty'       => 'JURUSAN KEPERAWATAN - PROGRAM STUDI D-III KEPERAWATAN',
                'title'         => 'INSTRUMEN PENILAIAN PENGKAJIAN PROSES KEPERAWATAN',
                'subtitle'      => 'Praktik Keperawatan Medikal Bedah (KMB) - Mengacu RPS WAT5.24.24',
                'identity'      => [
                    ['label' => 'Nama Mahasiswa',        'source' => 'student_name'],
                    ['label' => 'Diagnosa Medis Pasien', 'source' => 'case_focus'],
                    ['label' => 'NIM',                   'source' => 'student_nim'],
                    ['label' => 'Tanggal Pengkajian',    'source' => 'practice_date'],
                    ['label' => 'Ruang Rawat',           'source' => 'ward'],
                    ['label' => 'Penilai',               'source' => 'mentor_name'],
                ],
                'items' => [
                    ['no' => 1,  'group' => 'pengkajian', 'aspect' => 'Identitas pasien dan penanggung jawab dicatat lengkap'],
                    ['no' => 2,  'group' => 'pengkajian', 'aspect' => 'Riwayat kesehatan (keluhan utama, RPS/PQRST, RPD, riwayat keluarga, alergi) tergali lengkap'],
                    ['no' => 3,  'group' => 'pengkajian', 'aspect' => 'Anamnesis kebutuhan dasar yang relevan dengan kasus digali secara tepat'],
                    ['no' => 4,  'group' => 'pengkajian', 'aspect' => 'Pemeriksaan fisik per sistem/kebutuhan dasar dilakukan sistematis dan akurat'],
                    ['no' => 5,  'group' => 'pengkajian', 'aspect' => 'Pemeriksaan diagnostik/penunjang teridentifikasi dan relevan dengan kasus'],
                    ['no' => 6,  'group' => 'prosedur',   'aspect' => 'Tindakan keperawatan terkait dilakukan/direncanakan sesuai kondisi pasien'],
                    ['no' => 7,  'group' => 'pengkajian', 'aspect' => 'Pemeriksaan fisik umum head to toe dilakukan lengkap dan sistematis'],
                    ['no' => 8,  'group' => 'diagnosa',   'aspect' => 'Analisa data (DS, DO, etiologi, masalah keperawatan) tepat dan logis'],
                    ['no' => 9,  'group' => 'diagnosa',   'aspect' => 'Diagnosa keperawatan dirumuskan sesuai SDKI dan prioritas'],
                    ['no' => 10, 'group' => 'diagnosa',   'aspect' => 'Rencana keperawatan (SLKI & SIKI) sesuai diagnosa dan kondisi pasien'],
                    ['no' => 11, 'group' => 'evaluasi',   'aspect' => 'Implementasi dan evaluasi (SOAP) didokumentasikan dengan jelas'],
                ],
            ],
        ];
    }

    /** Ambil definisi satu instrumen; jatuh ke KDM bila stase tidak dikenali. */
    public static function for(string $stageType): array
    {
        $definitions = self::definitions();

        return $definitions[strtolower($stageType)] ?? $definitions['kdm'];
    }

    /**
     * Tentukan stase dari kode/nama mata kuliah, dengan pengkajian tersimpan
     * sebagai penentu utama bila sudah ada.
     */
    public static function resolveStageType(CareSession $session): string
    {
        $stored = $session->assessment?->stage_type;
        if ($stored && isset(self::definitions()[strtolower($stored)])) {
            return strtolower($stored);
        }

        $code = strtoupper($session->course?->code ?? '');
        $name = strtoupper($session->course?->name ?? '');

        if (str_contains($code, 'WAT5.31') || str_contains($name, 'KGD') || str_contains($name, 'DARURAT')) {
            return 'kgd';
        }

        if (str_contains($code, 'WAT5.24') || str_contains($name, 'KMB') || str_contains($name, 'BEDAH')) {
            return 'kmb';
        }

        return 'kdm';
    }

    /** Total skor maksimal instrumen (jumlah aspek x 4). */
    public static function maxScore(array $definition): int
    {
        return count($definition['items']) * self::MAX_ITEM_SCORE;
    }

    /**
     * Hitung rekapitulasi instrumen dari skor per aspek.
     *
     * @param  array<int|string, mixed>  $itemScores  Peta nomor aspek => skor 0-4.
     * @return array{items: array, total_score: int, max_score: int, nilai_akhir: float, kategori: string, is_complete: bool, rubric: array}
     */
    public static function summarize(array $definition, array $itemScores): array
    {
        $items     = [];
        $total     = 0;
        $filled    = 0;
        $groupSums = [];

        foreach ($definition['items'] as $item) {
            $raw   = $itemScores[$item['no']] ?? $itemScores[(string) $item['no']] ?? null;
            $score = self::normalizeScore($raw);

            if ($raw !== null && $raw !== '') {
                $filled++;
            }

            $total += $score;
            $items[] = $item + ['score' => $score];

            $group = $item['group'];
            $groupSums[$group] ??= ['sum' => 0, 'count' => 0];
            $groupSums[$group]['sum']   += $score;
            $groupSums[$group]['count'] += 1;
        }

        $maxScore = self::maxScore($definition);
        $nilai    = $maxScore > 0 ? round(($total / $maxScore) * 100, 2) : 0.0;

        // Setiap kelompok rubrik = rata-rata aspeknya, diskalakan dari 0-4 ke 0-100.
        $rubric = [];
        foreach (array_keys(self::RUBRIC_WEIGHTS) as $group) {
            $rubric[$group] = isset($groupSums[$group]) && $groupSums[$group]['count'] > 0
                ? round(($groupSums[$group]['sum'] / ($groupSums[$group]['count'] * self::MAX_ITEM_SCORE)) * 100, 2)
                : 0.0;
        }

        return [
            'items'       => $items,
            'total_score' => $total,
            'max_score'   => $maxScore,
            'nilai_akhir' => $nilai,
            'kategori'    => $nilai >= self::PASSING_SCORE ? 'Kompeten' : 'Belum Kompeten',
            'is_complete' => $filled === count($definition['items']),
            'rubric'      => $rubric,
        ];
    }

    /** Batasi skor aspek ke rentang bulat 0-4. */
    public static function normalizeScore(mixed $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return max(0, min(self::MAX_ITEM_SCORE, (int) round((float) $value)));
    }

    /**
     * Nilai akhir portofolio berbobot Sub-CPMK dari empat kelompok rubrik.
     *
     * @param  array<string, float|int|string>  $rubric
     */
    public static function weightedFinalScore(array $rubric): float
    {
        $score = 0.0;
        foreach (self::RUBRIC_WEIGHTS as $group => $weight) {
            $score += ((float) ($rubric[$group] ?? 0)) * $weight;
        }

        return round($score, 2);
    }

    /**
     * Isi otomatis kolom identitas instrumen dari data sesi asuhan.
     *
     * @return array<string, string>  Peta label kolom => nilai tercetak.
     */
    public static function identityValues(array $definition, CareSession $session, array $overrides = []): array
    {
        $payload = $session->assessment?->assessment_payload ?? [];
        $group   = $session->student?->studentGroups?->firstWhere('course_id', $session->course_id);

        $gender = $session->gender === 'L' ? 'Laki-laki' : 'Perempuan';

        $auto = [
            'student_name'       => $session->student?->name ?? '',
            'student_nim'        => $session->student?->nim_nip ?? '',
            'student_nim_group'  => trim(($session->student?->nim_nip ?? '') . ($group?->group_name ? ' / ' . $group->group_name : '')),
            'patient_age_gender' => trim(($session->age !== null ? $session->age . ' Tahun' : '') . ' / ' . $gender, ' /'),
            'practice_date'      => optional($session->submitted_at ?? $session->created_at)->translatedFormat('d F Y') ?? '',
            'mentor_name'        => $session->mentor?->name ?? '',
            'case_focus'         => self::caseFocus($session, $payload),
            'ward'               => $payload['ruang_rawat'] ?? $group?->group_name ?? '',
        ];

        $values = [];
        foreach ($definition['identity'] as $field) {
            $key = $field['source'];
            $values[$field['label']] = trim((string) ($overrides[$key] ?? $auto[$key] ?? ''));
        }

        return $values;
    }

    /**
     * Fokus kasus yang dicetak pada kolom "Kebutuhan Dasar"/"Kasus"/"Diagnosa Medis",
     * diambil dari diagnosa 3S prioritas utama bila ada, jika tidak dari keluhan utama.
     */
    private static function caseFocus(CareSession $session, array $payload): string
    {
        $primaryPlan = $session->carePlans?->first();
        if ($primaryPlan?->sdki?->title) {
            return $primaryPlan->sdki->title;
        }

        foreach (['diagnosa_medis', 'kebutuhan_dasar', 'keluhan_utama'] as $key) {
            if (! empty($payload[$key])) {
                return (string) $payload[$key];
            }
        }

        return '';
    }
}
