# Laporan Audit Kesiapan Arsitektur & Rekomendasi Teknis (Pre-Flight Architecture Audit)
## e-Askep Poltekkes Kemenkes Riau
**Sistem**: e-Askep Poltekkes Kemenkes Riau  
**Basis Kode**: Laravel 11 (Backend & Web) + Flutter 3.x (Mobile Android) + MySQL 8.0+  
**Cakupan**: Evaluasi Kesiapan Arsitektur, Skema Database, Binary PDF, dan Pipeline Pengembangan  
**Status**: Approved & Siap Eksekusi Sprint 1

---

## 1. Ringkasan Eksekutif Kesiapan Sistem

| ID | Komponen / Area | Kategori | Tingkat Urgensi | Estimasi Upaya | Status Kesiapan |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **AUD-01** | Inisiasi Backend Laravel 11 (`e-askep-backend`) | `setup` | **Tinggi (High)** | Menengah (*Medium*) | Folder backend masih kosong, siap diinisiasi dengan Composer & Laravel 11. |
| **AUD-02** | Inisiasi Mobile Flutter (`e-askep-mobile`) | `setup` | **Tinggi (High)** | Menengah (*Medium*) | Folder mobile masih kosong, siap diinisiasi dengan Flutter 3.x + BLoC + Drift. |
| **AUD-03** | Skema Migrasi 14 Tabel Basis Data | `database` | **Tinggi (High)** | Menengah (*Medium*) | DDL MySQL 8.0+ telah tervalidasi di PRD, siap dikonversi menjadi Migration Laravel. |
| **AUD-04** | Master Data Seeder 3S (PPNI) & Katalog SPO | `database` | **Tinggi (High)** | Menengah (*Medium*) | Perlu penyusunan seeder JSON untuk 149 diagnosa SDKI, SLKI, SIKI, dan SPO. |
| **AUD-05** | Integrasi PDF Engine `wkhtmltopdf` (Snappy) | `infrastructure` | **Sedang (Medium)** | Kecil (*Small*) | Perlu verifikasi path binary `wkhtmltopdf.exe` di Laragon Windows. |
| **AUD-06** | Setup Design System Shadcn UI + Tailwind | `frontend` | **Sedang (Medium)** | Kecil (*Small*) | Perlu integrasi token warna Kemenkes Toska (`#008D88`) dan Kuning Emas (`#EAB308`). |

---

## 2. Rincian Temuan & Rekomendasi Kesiapan Teknis

### [AUD-01] Inisiasi Proyek Backend Laravel 11
* **Area**: `e-askep-backend/`
* **Kategori**: `setup`
* **Tingkat Urgensi**: **Tinggi (High)**
* **Rekomendasi Tindakan**:
  1. Jalankan inisiasi Laravel 11: `composer create-project laravel/laravel e-askep-backend`.
  2. Pasang dependensi utama: `spatie/laravel-permission`, `laravel/sanctum`, `barryvdh/laravel-snappy`.
  3. Konfigurasi virtual host Laragon ke `http://e-askep-backend.test`.

---

### [AUD-02] Inisiasi Mobile Client Flutter 3.x
* **Area**: `e-askep-mobile/`
* **Kategori**: `setup`
* **Tingkat Urgensi**: **Tinggi (High)**
* **Rekomendasi Tindakan**:
  1. Jalankan inisiasi Flutter: `flutter create --org id.ac.poltekkesriau.easkep e-askep-mobile`.
  2. Tambahkan paket esensial: `flutter_bloc`, `drift`, `dio`, `signature`, `flutter_secure_storage`.
  3. Terapkan konfigurasi baseUrl API ke IP Laragon host atau `10.0.2.2:8000` (emulator).

---

### [AUD-03] Implementasi 14 Migrasi Database Relasional + JSON
* **Area**: `database/migrations/`
* **Kategori**: `database`
* **Tingkat Urgensi**: **Tinggi (High)**
* **Rekomendasi Tindakan**:
  1. Buat berkas migrasi terurut sesuai urutan dependensi Foreign Key:
     * `users` -> `courses` -> `student_groups` -> `master_sdki`/`slki`/`siki` -> `master_spo_procedures` -> `care_sessions` -> tabel anak pengkajian/tindakan/evaluasi.
  2. Pastikan kolom tipe native `JSON` memiliki nilai default `null` atau `"{}"`.
  3. Tambahkan index gabungan untuk optimasi query pencarian status asuhan.

---

### [AUD-04] Pembuatan Seeder Kamus 3S PPNI & Katalog SPO
* **Area**: `database/seeders/`
* **Kategori**: `database`
* **Tingkat Urgensi**: **Tinggi (High)**
* **Rekomendasi Tindakan**:
  1. Siapkan dataset SDKI, SLKI, SIKI format JSON di `database/seeders/data/`.
  2. Buat seeder prosedur SPO berdasarkan RPS fisik KGD (WAT5.31.24), KDM (WAT6.07.24), dan KMB (WAT5.24.24).
  3. Buat seeder akun default: Admin Prodi, Dosen/CI Penguji, dan Mahasiswa Pengkaji.

---

### [AUD-05] Konfigurasi Binary Snappy PDF di Lingkungan Windows Laragon
* **Area**: `config/snappy.php`
* **Kategori**: `infrastructure`
* **Tingkat Urgensi**: **Sedang (Medium)**
* **Rekomendasi Tindakan**:
  1. Pastikan binary `wkhtmltopdf.exe` terpasang di Laragon (biasanya di `C:/laragon/bin/wkhtmltopdf/bin/wkhtmltopdf.exe`).
  2. Atur variabel lingkungan di `.env`: `WKHTMLTOPDF_BINARY="C:/laragon/bin/wkhtmltopdf/bin/wkhtmltopdf.exe"`.
  3. Uji render contoh dokumen HTML ke PDF format A4.

---

### [AUD-06] Konfigurasi Tailwind CSS v4 & Tema Shadcn UI
* **Area**: `resources/css/app.css` & Tailwind config
* **Kategori**: `frontend`
* **Tingkat Urgensi**: **Sedang (Medium)**
* **Rekomendasi Tindakan**:
  1. Konfigurasi tema warna Poltekkes Riau:
     * Primary: `#008D88` (Toska Kemenkes)
     * Accent: `#EAB308` (Kuning Emas)
     * Base: `#F1F5F9` (Slate Canvas)
  2. Buat komponen Blade bergaya Shadcn UI: Floating Card (`rounded-2xl`), Pill Badges, Status Triase.

---

## 3. Rencana Aksi Sprint (Sprint Action Plan)

1. **Sprint 1 (Minggu 1–3)**: Setup backend Laravel 11, migrasi 14 tabel MySQL, seeder 3S & SPO, konfigurasi Shadcn UI Tailwind.
2. **Sprint 2 (Minggu 4–6)**: Portal Web Admin & Meja Telaah Dosen, otentikasi Sanctum & Spatie RBAC.
3. **Sprint 3 (Minggu 7–9)**: Inisiasi Flutter app, form wizard pengkajian KGD, KDM, KMB, auto-draft lokal (Drift).
4. **Sprint 4 (Minggu 10–11)**: Integrasi checklist tindakan SPO, batch e-paraf CI, canvas tanda tangan, penilaian rubrik Sub-CPMK.
5. **Sprint 5 (Minggu 12)**: Generator PDF Snappy format 1:1 Poltekkes Riau, integrasi QR Code verifikasi, UAT & deployment.
