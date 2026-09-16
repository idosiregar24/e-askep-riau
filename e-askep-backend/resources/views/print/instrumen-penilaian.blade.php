{{--
    Instrumen Penilaian Pengkajian Proses Keperawatan — lembar cetak resmi 1:1.
    Dirender oleh dompdf, sehingga gaya ditulis inline/CSS sederhana tanpa flexbox,
    grid, atau framework eksternal yang tidak didukung mesin render PDF.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $definition['title'] }} — {{ $session->student?->name }}</title>
    <style>
        /* Margin 15mm mengikuti pgMar 850 twip pada instrumen fisik. */
        @page { size: A4 portrait; margin: 15mm; }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 10pt;
            color: #000;
            margin: 0;
        }

        .center { text-align: center; }
        .right  { text-align: right; }
        .bold   { font-weight: bold; }

        .kop-institusi { font-size: 13pt; font-weight: bold; margin: 0 0 2px; }
        .kop-jurusan   { font-size: 10pt; font-weight: bold; margin: 0 0 10px; }
        .judul         { font-size: 12pt; font-weight: bold; margin: 0 0 3px; }
        .sub-judul     { font-size: 11pt; margin: 0 0 12px; }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.bordered th,
        table.bordered td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        table.bordered th {
            background: #d9d9d9;
            font-weight: bold;
            text-align: center;
        }

        .label-cell { background: #f2f2f2; font-weight: bold; }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            margin: 12px 0 6px;
        }

        .legend       { margin-top: 10px; font-size: 9pt; }
        .legend-title { font-weight: bold; font-size: 9pt; }
        .legend-item  { display: inline-block; margin-right: 14px; white-space: nowrap; }

        .ttd-block { margin-top: 22px; }
        .ttd-space { height: 52px; }

        .catatan-umum {
            margin-top: 14px;
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 9pt;
        }
    </style>
</head>
<body>

    {{-- Kop instrumen resmi --}}
    <p class="kop-institusi center">POLTEKKES KEMENKES RIAU</p>
    <p class="kop-jurusan center">{{ $definition['faculty'] }}</p>
    <p class="judul center">{{ $definition['title'] }}</p>
    <p class="sub-judul center">{{ $definition['subtitle'] }}</p>

    {{-- Tabel identitas: dua pasang label/nilai per baris, persis seperti berkas fisik --}}
    <table class="bordered">
        @foreach (array_chunk($identity, 2, true) as $pair)
            <tr>
                @foreach ($pair as $label => $value)
                    <td class="label-cell" style="width: 22%;">{{ $label }}</td>
                    <td style="width: 28%;">{{ $value !== '' ? $value : '' }}</td>
                @endforeach

                {{-- Lengkapi baris ganjil agar lebar kolom tetap sejajar --}}
                @if (count($pair) === 1)
                    <td class="label-cell" style="width: 22%;"></td>
                    <td style="width: 28%;"></td>
                @endif
            </tr>
        @endforeach
    </table>

    <p class="section-title">Kriteria Penilaian</p>

    <table class="bordered">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 58%;">Aspek yang Dinilai</th>
                <th style="width: 11%;">Skor<br>(0-4)</th>
                <th style="width: 25%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($summary['items'] as $item)
                <tr>
                    <td class="center">{{ $item['no'] }}</td>
                    <td>{{ $item['aspect'] }}</td>
                    <td class="center bold">{{ $item['score'] }}</td>
                    <td>{{ $notes[$item['no']] ?? $notes[(string) $item['no']] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Keterangan skala skor 0-4 --}}
    <div class="legend">
        <div class="legend-title">Keterangan Skor:</div>
        <div>
            @foreach ($scoreLegend as $value => $meaning)
                <span class="legend-item">{{ $value }} = {{ $meaning }}</span>
            @endforeach
        </div>
    </div>

    {{-- Rekapitulasi nilai akhir --}}
    <table class="bordered" style="margin-top: 12px;">
        <tr>
            <td class="bold" style="width: 72%;">Total Skor (maksimal {{ $summary['max_score'] }})</td>
            <td class="center bold" style="width: 28%;">{{ $summary['total_score'] }}</td>
        </tr>
        <tr>
            <td class="bold">Nilai Akhir = (Total Skor / {{ $summary['max_score'] }}) x 100</td>
            <td class="center bold">{{ number_format($summary['nilai_akhir'], 2) }}</td>
        </tr>
        <tr>
            <td class="bold">Kategori Kelulusan (Kompeten jika Nilai &ge; {{ $passingScore }} / Belum Kompeten jika &lt; {{ $passingScore }})</td>
            <td class="center bold">{{ $summary['kategori'] }}</td>
        </tr>
    </table>

    @if (! empty($generalNotes))
        <div class="catatan-umum">
            <span class="bold">Umpan Balik Pembimbing:</span> {{ $generalNotes }}
        </div>
    @endif

    {{-- Blok tanda tangan penilai --}}
    <table class="ttd-block">
        <tr>
            <td style="width: 58%;"></td>
            <td style="width: 42%;" class="right">
                {{ $footer['place'] }}, {{ $footer['date'] }}<br>
                Penilai / Pembimbing,

                <div class="ttd-space">
                    @if (! empty($footer['signature_path']))
                        <img src="{{ $footer['signature_path'] }}" alt="Paraf digital penilai" style="height: 58px;">
                    @endif
                </div>

                <span class="bold">({{ $footer['assessor'] !== '' ? $footer['assessor'] : '____________________________' }})</span><br>
                NIDN/NIP. {{ $footer['assessor_id'] }}
            </td>
        </tr>
    </table>

</body>
</html>
