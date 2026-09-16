<?php

namespace Database\Seeders;

use App\Models\MasterSdki;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use RuntimeException;

/**
 * Seeder master Standar Diagnosis Keperawatan Indonesia (SDKI) PPNI.
 *
 * Memuat 149 diagnosis keperawatan lengkap beserta definisi baku, tanda & gejala
 * mayor (DS/DO), faktor risiko, dan penyebab (etiologi).
 *
 * Sumber data: https://perawat.org/149-diagnosis-keperawatan-indonesia/
 * Berkas data: database/data/sdki.json
 *
 * Kode diagnosis mengikuti penomoran resmi SDKI (D.0001 - D.0149), terurut per
 * kategori dan subkategori:
 *   Fisiologis  - Respirasi, Sirkulasi, Nutrisi dan Cairan, Eliminasi,
 *                 Aktivitas dan Istirahat, Neurosensori, Reproduksi dan Seksualitas
 *   Psikologis  - Nyeri dan Kenyamanan, Integritas Ego, Pertumbuhan dan Perkembangan
 *   Perilaku    - Kebersihan Diri, Penyuluhan dan Pembelajaran
 *   Relasional  - Interaksi Sosial
 *   Lingkungan  - Keamanan dan Proteksi
 */
class MasterSdkiSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/sdki.json');

        if (! File::exists($path)) {
            throw new RuntimeException("Berkas data SDKI tidak ditemukan pada: {$path}");
        }

        $diagnoses = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($diagnoses) || $diagnoses === []) {
            throw new RuntimeException('Berkas data SDKI kosong atau tidak valid.');
        }

        // updateOrCreate agar seeder aman dijalankan berulang tanpa menduplikasi
        // maupun menghapus relasi rencana asuhan yang sudah menunjuk diagnosis ini.
        foreach ($diagnoses as $row) {
            MasterSdki::updateOrCreate(
                ['code' => $row['code']],
                [
                    'title'        => $row['title'],
                    'category'     => $row['category'],
                    'sub_category' => $row['sub_category'],
                    'definition'   => $row['definition'] ?? null,
                    'major_signs'  => $row['major_signs'] ?? ['subjective' => [], 'objective' => []],
                    'minor_signs'  => $row['minor_signs'] ?? ['subjective' => [], 'objective' => []],
                    'risk_factors' => $row['risk_factors'] ?? [],
                    'causes'       => $row['causes'] ?? [],
                    'source_url'   => $row['source_url'] ?? null,
                ]
            );
        }

        $this->command?->info(sprintf(
            'Master SDKI tersinkronisasi: %d diagnosis keperawatan (%s - %s).',
            count($diagnoses),
            $diagnoses[0]['code'],
            $diagnoses[count($diagnoses) - 1]['code']
        ));
    }
}
