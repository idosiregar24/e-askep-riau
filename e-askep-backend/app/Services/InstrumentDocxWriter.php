<?php

namespace App\Services;

use App\Support\PenilaianInstrument;
use RuntimeException;
use ZipArchive;

/**
 * Penulis dokumen Word (.docx) untuk Instrumen Penilaian Pengkajian Proses Keperawatan.
 *
 * Dokumen dibangun langsung sebagai paket OOXML (WordprocessingML) agar tata letak
 * kop, tabel identitas, tabel aspek penilaian, rekapitulasi nilai, dan blok tanda
 * tangan identik 1:1 dengan berkas instrumen fisik Poltekkes Kemenkes Riau —
 * tanpa bergantung pada pustaka pihak ketiga.
 */
class InstrumentDocxWriter
{
    /**
     * Lebar area cetak A4 potret dikurangi margin kiri/kanan, dalam twip (1/20 pt).
     * 11906 (A4) - 2 x 850 (margin instrumen asli) = 10206.
     */
    private const CONTENT_WIDTH = 10206;

    /**
     * Render instrumen menjadi berkas .docx dan kembalikan isinya sebagai string biner.
     *
     * @param  array  $definition  Definisi instrumen dari PenilaianInstrument.
     * @param  array  $summary     Rekapitulasi skor dari PenilaianInstrument::summarize().
     * @param  array  $identity    Peta label kolom identitas => nilai.
     * @param  array  $notes       Peta nomor aspek => catatan penilai.
     * @param  array  $footer      ['place' => ..., 'date' => ..., 'assessor' => ..., 'assessor_id' => ...]
     */
    public function render(array $definition, array $summary, array $identity, array $notes = [], array $footer = []): string
    {
        $document = $this->buildDocumentXml($definition, $summary, $identity, $notes, $footer);

        $path = tempnam(sys_get_temp_dir(), 'instrumen_') ?: throw new RuntimeException('Gagal membuat berkas sementara DOCX.');

        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Gagal membuka arsip DOCX untuk ditulis.');
        }

        foreach ($this->packageParts($document) as $entry => $contents) {
            $zip->addFromString($entry, $contents);
        }
        $zip->close();

        $binary = file_get_contents($path);
        @unlink($path);

        if ($binary === false) {
            throw new RuntimeException('Gagal membaca berkas DOCX yang dihasilkan.');
        }

        return $binary;
    }

    /** Nama berkas unduhan yang rapi dan aman untuk sistem berkas. */
    public function filename(array $definition, string $studentName, string $extension = 'docx'): string
    {
        $slug = preg_replace('/[^A-Za-z0-9]+/', '-', $studentName) ?: 'Mahasiswa';

        return sprintf(
            'Instrumen-Penilaian-%s-%s.%s',
            strtoupper($definition['stage_label']),
            trim($slug, '-'),
            $extension
        );
    }

    /* --------------------------------------------------------------------- */
    /* Penyusunan body dokumen                                               */
    /* --------------------------------------------------------------------- */

    private function buildDocumentXml(array $definition, array $summary, array $identity, array $notes, array $footer): string
    {
        $body = '';

        // --- Kop instrumen -------------------------------------------------
        $body .= $this->paragraph('POLTEKKES KEMENKES RIAU', ['bold' => true, 'size' => 26, 'align' => 'center']);
        $body .= $this->paragraph($definition['faculty'], ['bold' => true, 'size' => 20, 'align' => 'center']);
        $body .= $this->paragraph($definition['title'], ['bold' => true, 'size' => 24, 'align' => 'center', 'spaceBefore' => 160]);
        $body .= $this->paragraph($definition['subtitle'], ['size' => 22, 'align' => 'center', 'spaceAfter' => 240]);

        // --- Tabel identitas (2 pasang label/nilai per baris) ---------------
        $labelWidth = (int) round(self::CONTENT_WIDTH * 0.22);
        $valueWidth = (int) round(self::CONTENT_WIDTH * 0.28);

        $identityRows = [];
        $pairs = array_chunk(array_slice($identity, 0, count($definition['identity']), true), 2, true);
        foreach ($pairs as $pair) {
            $cells = [];
            foreach ($pair as $label => $value) {
                $cells[] = $this->cell($label, $labelWidth, ['bold' => true, 'shade' => 'F2F2F2']);
                $cells[] = $this->cell($value !== '' ? $value : ' ', $valueWidth);
            }
            // Lengkapi baris ganjil agar lebar tabel tetap konsisten.
            while (count($cells) < 4) {
                $cells[] = $this->cell(' ', $labelWidth, ['shade' => 'F2F2F2']);
                $cells[] = $this->cell(' ', $valueWidth);
            }
            $identityRows[] = $this->row($cells);
        }
        $body .= $this->table($identityRows);
        $body .= $this->paragraph('', ['size' => 12]);

        // --- Tabel kriteria penilaian --------------------------------------
        $body .= $this->paragraph('Kriteria Penilaian', ['bold' => true, 'size' => 22, 'spaceBefore' => 120, 'spaceAfter' => 120]);

        $wNo     = (int) round(self::CONTENT_WIDTH * 0.06);
        $wAspect = (int) round(self::CONTENT_WIDTH * 0.58);
        $wScore  = (int) round(self::CONTENT_WIDTH * 0.11);
        $wNote   = self::CONTENT_WIDTH - $wNo - $wAspect - $wScore;

        $rows = [$this->row([
            $this->cell('No', $wNo, ['bold' => true, 'shade' => 'D9D9D9', 'align' => 'center']),
            $this->cell('Aspek yang Dinilai', $wAspect, ['bold' => true, 'shade' => 'D9D9D9', 'align' => 'center']),
            $this->cell("Skor\n(0-4)", $wScore, ['bold' => true, 'shade' => 'D9D9D9', 'align' => 'center']),
            $this->cell('Catatan', $wNote, ['bold' => true, 'shade' => 'D9D9D9', 'align' => 'center']),
        ], ['header' => true])];

        foreach ($summary['items'] as $item) {
            $note = trim((string) ($notes[$item['no']] ?? $notes[(string) $item['no']] ?? ''));

            $rows[] = $this->row([
                $this->cell((string) $item['no'], $wNo, ['align' => 'center']),
                $this->cell($item['aspect'], $wAspect),
                $this->cell((string) $item['score'], $wScore, ['align' => 'center', 'bold' => true]),
                $this->cell($note !== '' ? $note : ' ', $wNote),
            ]);
        }
        $body .= $this->table($rows);

        // --- Keterangan skor ------------------------------------------------
        $legend = [];
        foreach (PenilaianInstrument::SCORE_LEGEND as $value => $meaning) {
            $legend[] = $value . ' = ' . $meaning;
        }
        $body .= $this->paragraph('Keterangan Skor:', ['bold' => true, 'size' => 18, 'spaceBefore' => 200]);
        $body .= $this->paragraph(implode('   ', $legend), ['size' => 18, 'spaceAfter' => 240]);

        // --- Rekapitulasi nilai --------------------------------------------
        $wRecapLabel = (int) round(self::CONTENT_WIDTH * 0.72);
        $wRecapValue = self::CONTENT_WIDTH - $wRecapLabel;

        $body .= $this->table([
            $this->row([
                $this->cell(sprintf('Total Skor (maksimal %d)', $summary['max_score']), $wRecapLabel, ['bold' => true]),
                $this->cell((string) $summary['total_score'], $wRecapValue, ['align' => 'center', 'bold' => true]),
            ]),
            $this->row([
                $this->cell(sprintf('Nilai Akhir = (Total Skor / %d) x 100', $summary['max_score']), $wRecapLabel, ['bold' => true]),
                $this->cell(number_format($summary['nilai_akhir'], 2), $wRecapValue, ['align' => 'center', 'bold' => true]),
            ]),
            $this->row([
                $this->cell(
                    sprintf('Kategori Kelulusan (Kompeten jika Nilai ≥ %d / Belum Kompeten jika < %d)', PenilaianInstrument::PASSING_SCORE, PenilaianInstrument::PASSING_SCORE),
                    $wRecapLabel,
                    ['bold' => true]
                ),
                $this->cell($summary['kategori'], $wRecapValue, ['align' => 'center', 'bold' => true]),
            ]),
        ]);

        // --- Blok tanda tangan ---------------------------------------------
        $place = $footer['place'] ?? 'Pekanbaru';
        $date  = $footer['date'] ?? '';

        $body .= $this->paragraph(trim($place . ', ' . $date), ['align' => 'right', 'spaceBefore' => 400]);
        $body .= $this->paragraph('Penilai / Pembimbing,', ['align' => 'right']);
        $body .= $this->paragraph('', ['size' => 20, 'spaceAfter' => 600]);
        $body .= $this->paragraph('(' . ($footer['assessor'] ?? '____________________________') . ')', ['align' => 'right', 'bold' => true]);
        $body .= $this->paragraph('NIDN/NIP. ' . ($footer['assessor_id'] ?? ''), ['align' => 'right']);

        // --- Pengaturan halaman A4 potret, margin 2 cm ----------------------
        $body .= '<w:sectPr>'
            . '<w:pgSz w:w="11906" w:h="16838"/>'
            . '<w:pgMar w:top="850" w:right="850" w:bottom="850" w:left="850" w:header="708" w:footer="708" w:gutter="0"/>'
            . '</w:sectPr>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            . '<w:body>' . $body . '</w:body>'
            . '</w:document>';
    }

    /* --------------------------------------------------------------------- */
    /* Primitif WordprocessingML                                             */
    /* --------------------------------------------------------------------- */

    /**
     * Satu paragraf. Baris baru pada $text dirender sebagai <w:br/>.
     *
     * @param  array{bold?: bool, size?: int, align?: string, spaceBefore?: int, spaceAfter?: int}  $opts
     */
    private function paragraph(string $text, array $opts = []): string
    {
        $spacing = '<w:spacing w:before="' . ($opts['spaceBefore'] ?? 0) . '" w:after="' . ($opts['spaceAfter'] ?? 60) . '"/>';
        $align   = isset($opts['align']) ? '<w:jc w:val="' . $opts['align'] . '"/>' : '';

        return '<w:p><w:pPr>' . $spacing . $align . '</w:pPr>' . $this->run($text, $opts) . '</w:p>';
    }

    /** Satu run teks dengan properti huruf; menangani newline dan spasi signifikan. */
    private function run(string $text, array $opts = []): string
    {
        $size = $opts['size'] ?? 20; // setengah-poin: 20 = 10pt

        $props = '<w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/>'
            . (! empty($opts['bold']) ? '<w:b/>' : '')
            . '<w:sz w:val="' . $size . '"/><w:szCs w:val="' . $size . '"/>'
            . '</w:rPr>';

        $segments = explode("\n", $text);
        $content  = '';
        foreach ($segments as $i => $segment) {
            if ($i > 0) {
                $content .= '<w:br/>';
            }
            $content .= '<w:t xml:space="preserve">' . $this->escape($segment) . '</w:t>';
        }

        return '<w:r>' . $props . $content . '</w:r>';
    }

    /** @param  array{bold?: bool, size?: int, align?: string, shade?: string}  $opts */
    private function cell(string $text, int $width, array $opts = []): string
    {
        $shade = isset($opts['shade'])
            ? '<w:shd w:val="clear" w:color="auto" w:fill="' . $opts['shade'] . '"/>'
            : '';

        return '<w:tc>'
            . '<w:tcPr>'
            . '<w:tcW w:w="' . $width . '" w:type="dxa"/>'
            . $shade
            . '<w:vAlign w:val="center"/>'
            . '</w:tcPr>'
            . $this->paragraph($text, $opts + ['spaceAfter' => 20])
            . '</w:tc>';
    }

    /** @param  array{header?: bool}  $opts */
    private function row(array $cells, array $opts = []): string
    {
        // Baris header diulang otomatis bila tabel terpotong antar halaman.
        $props = ! empty($opts['header'])
            ? '<w:trPr><w:tblHeader/><w:cantSplit/></w:trPr>'
            : '<w:trPr><w:cantSplit/></w:trPr>';

        return '<w:tr>' . $props . implode('', $cells) . '</w:tr>';
    }

    private function table(array $rows): string
    {
        $borders = '<w:tblBorders>';
        foreach (['top', 'left', 'bottom', 'right', 'insideH', 'insideV'] as $edge) {
            $borders .= '<w:' . $edge . ' w:val="single" w:sz="6" w:space="0" w:color="000000"/>';
        }
        $borders .= '</w:tblBorders>';

        return '<w:tbl>'
            . '<w:tblPr>'
            . '<w:tblW w:w="' . self::CONTENT_WIDTH . '" w:type="dxa"/>'
            . '<w:tblLayout w:type="fixed"/>'
            . $borders
            . '<w:tblCellMar>'
            . '<w:top w:w="60" w:type="dxa"/><w:left w:w="90" w:type="dxa"/>'
            . '<w:bottom w:w="60" w:type="dxa"/><w:right w:w="90" w:type="dxa"/>'
            . '</w:tblCellMar>'
            . '</w:tblPr>'
            . implode('', $rows)
            . '</w:tbl>';
    }

    private function escape(string $value): string
    {
        // Buang karakter kontrol yang ilegal di XML 1.0 sebelum meng-escape.
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $value) ?? '';

        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    /* --------------------------------------------------------------------- */
    /* Kerangka paket OOXML                                                  */
    /* --------------------------------------------------------------------- */

    /** @return array<string, string> Peta nama entri zip => isi berkas. */
    private function packageParts(string $document): array
    {
        return [
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                . '<Default Extension="xml" ContentType="application/xml"/>'
                . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
                . '<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>'
                . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
                . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
                . '</Types>',

            '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
                . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
                . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
                . '</Relationships>',

            'word/_rels/document.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
                . '</Relationships>',

            'word/styles.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
                . '<w:docDefaults><w:rPrDefault><w:rPr>'
                . '<w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/>'
                . '<w:sz w:val="20"/><w:szCs w:val="20"/>'
                . '</w:rPr></w:rPrDefault></w:docDefaults>'
                . '<w:style w:type="paragraph" w:default="1" w:styleId="Normal">'
                . '<w:name w:val="Normal"/>'
                . '<w:pPr><w:spacing w:after="60" w:line="240" w:lineRule="auto"/></w:pPr>'
                . '</w:style>'
                . '</w:styles>',

            'docProps/core.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" '
                . 'xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" '
                . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
                . '<dc:title>Instrumen Penilaian Pengkajian Proses Keperawatan</dc:title>'
                . '<dc:creator>e-Askep Poltekkes Kemenkes Riau</dc:creator>'
                . '<cp:lastModifiedBy>e-Askep Poltekkes Kemenkes Riau</cp:lastModifiedBy>'
                . '<dcterms:created xsi:type="dcterms:W3CDTF">' . gmdate('Y-m-d\TH:i:s\Z') . '</dcterms:created>'
                . '<dcterms:modified xsi:type="dcterms:W3CDTF">' . gmdate('Y-m-d\TH:i:s\Z') . '</dcterms:modified>'
                . '</cp:coreProperties>',

            'docProps/app.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" '
                . 'xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
                . '<Application>e-Askep Poltekkes Kemenkes Riau</Application>'
                . '<Company>Politeknik Kesehatan Kemenkes Riau</Company>'
                . '</Properties>',

            'word/document.xml' => $document,
        ];
    }
}
