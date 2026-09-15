<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\MasterSdki;
use App\Models\MasterSiki;
use App\Models\MasterSlki;
use App\Models\MasterSpoProcedure;
use App\Models\StudentGroup;
use App\Models\User;
use Illuminate\Database\Seeder;

class DataSeeder extends Seeder
{
    /**
     * Seed master data, kurikulum mata kuliah, kelompok bimbingan, SDKI/SLKI/SIKI, dan SPO.
     */
    public function run(): void
    {
        // 1. SEED MATA KULIAH / STASE KURIKULUM (SESUAI PRD & RPS)
        $courseKGD = Course::firstOrCreate(
            ['code' => 'WAT5.31.24'],
            [
                'name'          => 'Keperawatan Gawat Darurat (KGD)',
                'program_study' => 'D-III Keperawatan',
                'academic_year' => '2025/2026 Ganjil',
            ]
        );

        $courseKDM = Course::firstOrCreate(
            ['code' => 'WAT6.07.24'],
            [
                'name'          => 'Kebutuhan Dasar Manusia (KDM)',
                'program_study' => 'Sarjana Terapan Keperawatan',
                'academic_year' => '2025/2026 Ganjil',
            ]
        );

        $courseKMB = Course::firstOrCreate(
            ['code' => 'WAT5.24.24'],
            [
                'name'          => 'Keperawatan Medikal Bedah (KMB)',
                'program_study' => 'D-III Keperawatan',
                'academic_year' => '2025/2026 Ganjil',
            ]
        );

        // 2. SEED KELOMPOK PRAKTIK & BIMBINGAN
        $dosen = User::where('email', 'dosen@poltekkes-riau.ac.id')->first();
        $mhs   = User::where('email', 'mahasiswa@poltekkes-riau.ac.id')->first();

        if ($dosen && $mhs) {
            StudentGroup::firstOrCreate(
                [
                    'course_id'       => $courseKGD->id,
                    'mentor_dosen_id' => $dosen->id,
                    'student_id'      => $mhs->id,
                ],
                [
                    'group_name'      => 'Kelompok 1 - IGD RSUD Arifin Achmad',
                ]
            );
        }

        // 3. SEED MASTER SDKI PPNI
        MasterSdki::firstOrCreate(
            ['code' => 'D.0001'],
            [
                'title'        => 'Bersihan Jalan Napas Tidak Efektif',
                'category'     => 'Fisiologis',
                'sub_category' => 'Respirasi',
                'major_signs'  => [
                    'subjective' => [],
                    'objective'  => ['Batuk tidak efektif', 'Tidak mampu batuk', 'Sputum berlebih', 'Mengi / Wheezing', 'Ronkhi basah'],
                ],
                'minor_signs'  => [
                    'subjective' => ['Dispnea', 'Sulit bicara', 'Ortopnea'],
                    'objective'  => ['Gelisah', 'Sianosis', 'Bunyi napas menurun', 'Frekuensi napas berubah', 'Pola napas berubah'],
                ],
            ]
        );

        MasterSdki::firstOrCreate(
            ['code' => 'D.0005'],
            [
                'title'        => 'Pola Napas Tidak Efektif',
                'category'     => 'Fisiologis',
                'sub_category' => 'Respirasi',
                'major_signs'  => [
                    'subjective' => ['Dispnea'],
                    'objective'  => ['Penggunaan otot bantu napas', 'Fase ekspirasi memanjang', 'Pola napas abnormal (takipnea/bradipnea)'],
                ],
                'minor_signs'  => [
                    'subjective' => ['Ortopnea'],
                    'objective'  => ['Pernapasan cuping hidung', 'Tekanan ekspirasi menurun', 'Ekskursi dada berubah'],
                ],
            ]
        );

        MasterSdki::firstOrCreate(
            ['code' => 'D.0077'],
            [
                'title'        => 'Nyeri Akut',
                'category'     => 'Psikologis',
                'sub_category' => 'Nyeri dan Kenyamanan',
                'major_signs'  => [
                    'subjective' => ['Mengeluh nyeri'],
                    'objective'  => ['Tampak meringis', 'Bersikap protektif (waspada, posisi menghindari nyeri)', 'Gelisah', 'Frekuensi nadi meningkat', 'Sulit tidur'],
                ],
                'minor_signs'  => [
                    'subjective' => [],
                    'objective'  => ['Tekanan darah meningkat', 'Pola napas berubah', 'Nafsu makan berubah', 'Proses berpikir terganggu', 'Menarik diri'],
                ],
            ]
        );

        // 4. SEED MASTER SLKI PPNI
        MasterSlki::firstOrCreate(
            ['code' => 'L.01001'],
            [
                'title'      => 'Bersihan Jalan Napas',
                'indicators' => [
                    'Batuk efektif meningkat (skala 5)',
                    'Produksi sputum menurun (skala 5)',
                    'Mengi menurun (skala 5)',
                    'Ronkhi basah menurun (skala 5)',
                    'Dispnea menurun (skala 5)',
                    'Frekuensi napas membaik (skala 5)',
                ],
            ]
        );

        MasterSlki::firstOrCreate(
            ['code' => 'L.08066'],
            [
                'title'      => 'Tingkat Nyeri',
                'indicators' => [
                    'Keluhan nyeri menurun (skala 5)',
                    'Meringis menurun (skala 5)',
                    'Sikap protektif menurun (skala 5)',
                    'Gelisah menurun (skala 5)',
                    'Frekuensi nadi membaik (skala 5)',
                ],
            ]
        );

        // 5. SEED MASTER SIKI PPNI
        MasterSiki::firstOrCreate(
            ['code' => 'I.01011'],
            [
                'title'   => 'Manajemen Jalan Napas',
                'actions' => [
                    'observasi'   => ['Monitor pola napas (frekuensi, kedalaman, usaha napas)', 'Monitor bunyi napas tambahan', 'Monitor sputum (jumlah, warna, aroma)'],
                    'terapeutik'  => ['Pertahankan kepatenan jalan napas dengan head-tilt chin-lift atau jaw-thrust', 'Posisikan semi-fowler atau fowler', 'Berikan oksigenasi sesuai indikasi'],
                    'edukasi'     => ['Anjurkan asupan cairan 2000 ml/hari jika tidak kontraindikasi', 'Ajarkan teknik batuk efektif'],
                    'kolaborasi'  => ['Kolaborasi pemberian bronkodilator, ekspektoran, atau mukolitik jika perlu'],
                ],
            ]
        );

        MasterSiki::firstOrCreate(
            ['code' => 'I.08238'],
            [
                'title'   => 'Manajemen Nyeri',
                'actions' => [
                    'observasi'   => ['Identifikasi lokasi, karakteristik, durasi, frekuensi, kualitas, dan intensitas nyeri (PQRST)', 'Identifikasi skala nyeri (0-10)'],
                    'terapeutik'  => ['Berikan teknik nonfarmakologis (relaksasi napas dalam, kompres hangat/dingin)', 'Fasilitasi istirahat dan tidur'],
                    'edukasi'     => ['Jelaskan penyebab, periode, dan pemicu nyeri', 'Ajarkan teknik manajemen stres'],
                    'kolaborasi'  => ['Kolaborasi pemberian analgetik jika perlu'],
                ],
            ]
        );

        // 6. SEED KATALOG SPO PROSEDUR TINDAKAN (KGD & KDM)
        $spoList = [
            // KGD (Sub-CPMK 2 - 13)
            ['course_id' => $courseKGD->id, 'sub_cpmk_reference' => 'Sub-CPMK 2', 'domain_category' => 'Live Saving', 'procedure_name' => 'Resusitasi Jantung Paru (RJP/BHD) Dewasa'],
            ['course_id' => $courseKGD->id, 'sub_cpmk_reference' => 'Sub-CPMK 3', 'domain_category' => 'Airway & Breathing', 'procedure_name' => 'Pemasangan Oropharyngeal Airway (OPA / Guedel)'],
            ['course_id' => $courseKGD->id, 'sub_cpmk_reference' => 'Sub-CPMK 3', 'domain_category' => 'Airway & Breathing', 'procedure_name' => 'Penghisapan Lendir (Suctioning Endotrakeal/Orofaring)'],
            ['course_id' => $courseKGD->id, 'sub_cpmk_reference' => 'Sub-CPMK 4', 'domain_category' => 'Circulation', 'procedure_name' => 'Perekaman EKG 12 Lead'],
            ['course_id' => $courseKGD->id, 'sub_cpmk_reference' => 'Sub-CPMK 5', 'domain_category' => 'Circulation', 'procedure_name' => 'Pemasangan Infus Jalur Ganda & Resusitasi Cairan'],
            ['course_id' => $courseKGD->id, 'sub_cpmk_reference' => 'Sub-CPMK 6', 'domain_category' => 'Trauma Care', 'procedure_name' => 'Imobilisasi Servikal (Pemasangan Cervical Collar) & Log Roll'],
            // KDM (Sub-CPMK 1 - 9)
            ['course_id' => $courseKDM->id, 'sub_cpmk_reference' => 'Sub-CPMK 1', 'domain_category' => 'Vital Signs', 'procedure_name' => 'Pemeriksaan Tanda-Tanda Vital Lengkap'],
            ['course_id' => $courseKDM->id, 'sub_cpmk_reference' => 'Sub-CPMK 2', 'domain_category' => 'Medication', 'procedure_name' => 'Pemberian Obat Injeksi IV / IM dengan Prinsip 6 Benar'],
            ['course_id' => $courseKDM->id, 'sub_cpmk_reference' => 'Sub-CPMK 3', 'domain_category' => 'Oxygenation', 'procedure_name' => 'Pemasangan Kanula Nasal Oksigen & Masker'],
            ['course_id' => $courseKDM->id, 'sub_cpmk_reference' => 'Sub-CPMK 4', 'domain_category' => 'Elimination', 'procedure_name' => 'Pemasangan Kateter Urin Menetap (Foley Catheter)'],
            // KMB
            ['course_id' => $courseKMB->id, 'sub_cpmk_reference' => 'Sub-CPMK 1', 'domain_category' => 'Wound Care', 'procedure_name' => 'Perawatan Luka Bedah Aseptik & Penggantian Balutan'],
            ['course_id' => $courseKMB->id, 'sub_cpmk_reference' => 'Sub-CPMK 2', 'domain_category' => 'Perioperative', 'procedure_name' => 'Persiapan Pra-Bedah & Pengisian Surgical Safety Checklist'],
        ];

        foreach ($spoList as $spo) {
            MasterSpoProcedure::firstOrCreate(
                [
                    'course_id'      => $spo['course_id'],
                    'procedure_name' => $spo['procedure_name'],
                ],
                $spo
            );
        }
    }
}
