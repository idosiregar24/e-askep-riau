# CLAUDE.md — e-Askep Poltekkes Kemenkes Riau

Panduan utama untuk AI Agent dan Software Engineer dalam mengembangkan sistem **e-Askep Poltekkes Kemenkes Riau** (Sistem Digitalisasi Asuhan Keperawatan & Logbook Praktikum Klinis Terintegrasi).

---

## 1. Ringkasan Eksekutif & Arsitektur Sistem

* **Nama Produk**: e-Askep Poltekkes Kemenkes Riau
* **Tagline**: *Presisi Pengkajian, Tertib Prosedur, dan Evaluasi Klinis Tanpa Kertas.*
* **Pola Arsitektur**: **Hybrid Monolith + Mobile API-First**
  * **Backend & Web Portal**: Laravel 11 (PHP 8.3+) untuk Web Admin & Dosen/CI serta RESTful API Engine.
  * **Mobile Client**: Flutter 3.x (Dart) untuk Mahasiswa dan Dosen/CI di bangsal klinis (Offline-First).
  * **Basis Data**: MySQL 8.0+ (14 tabel normalisasi + native JSON payload untuk form pengkajian dinamis).
* **Filosofi UI/UX**: **Clinical Clean Minimalism (Shadcn UI style)**
  * Latar putih bersih (*pure white*) dengan Floating Card Frame (`rounded-2xl`, outline border halus, micro shadow).
  * Palet identitas resmi Poltekkes Kemenkes Riau & warna semantik triase medis.

---

## 2. Struktur Monorepo (Laragon Workspace)

```text
C:/laragon/www/e-askep-riau/
├── .claude/                       <--- Konfigurasi, rules, skills, testing, & PRD
│   ├── audit/                     <--- Laporan audit arsitektur & kesiapan sistem
│   ├── plan/                      <--- PRD Final Design & instrumen RPS fisik
│   ├── rules/                     <--- Aturan baku (backend, frontend, database, security)
│   ├── skills/                    <--- Panduan workflow fitur (pengkajian, 3S, e-paraf)
│   ├── testing/                   <--- Matriks pengujian terperinci 10 modul
│   ├── CLAUDE.md                  <--- Panduan kerja agen (file ini)
│   └── README.md                  <--- Dokumentasi tata kelola direktori .claude
├── e-askep-backend/               <--- Laravel 11 (Web Admin/Dosen + REST API)
└── e-askep-mobile/                <--- Flutter 3.x Android Application (BLoC + Drift)
```

---

## 3. Tech Stack & Komponen Kunci

### Backend (`e-askep-backend`)
* **Framework**: Laravel 11 dengan PHP 8.3+
* **Web UI (Admin & Dosen)**: Laravel Blade + Livewire v3 / Alpine.js bergaya Shadcn UI (Tailwind CSS v4).
* **API Engine & Auth**: Laravel Sanctum (Token-based stateless authentication untuk mobile).
* **Role & Otorisasi**: `spatie/laravel-permission` (Role: `admin`, `dosen`, `mahasiswa`).
* **PDF Render Engine**: `barryvdh/laravel-snappy` (menggunakan binary `wkhtmltopdf`) untuk dokumen cetak presisi 1:1 fisik Poltekkes Riau.
* **Database Driver**: MySQL PDO, InnoDB, `utf8mb4_unicode_ci`.

### Mobile (`e-askep-mobile`)
* **Framework**: Flutter 3.x (Dart 3.x)
* **Target Platform**: Android (Smartphone & Tablet klinis).
* **State Management**: BLoC (`flutter_bloc`) / Cubit.
* **Offline-First Storage**: `drift` (SQLite) / `isar` untuk auto-draft formulir klinis di bangsal.
* **HTTP Client**: `dio` dengan Sanctum token interceptor, refresh token, & error boundary.
* **Signature Pad**: `signature` package (ekspor PNG bitmap transparan untuk paraf digital).

---

## 4. Design System & Palet Warna Poltekkes Riau

Wajib digunakan secara konsisten pada antarmuka Web dan Mobile:

| Token Warna | Nilai HEX | Penggunaan UI |
|---|---|---|
| **Primary / Brand** | `#008D88` | Hijau Toska Kemenkes: tombol utama, menu aktif, header modul |
| **Primary Hover** | `#00736F` | Status hover tombol utama |
| **Primary Tint** | `#E6F5F4` | Background kapsul pill tab aktif, light badge |
| **Secondary / Accent** | `#EAB308` | Kuning Emas Poltekkes: aksen penilaian, highlight catatan |
| **Base Canvas** | `#F1F5F9` | Slate-100 lembut: latar belakang luar kanvas aplikasi |
| **Main Card / Surface** | `#FFFFFF` | Pure White: kartu konten kerja, form, dialog |
| **Sidebar Surface** | `#FAFAFA` / `#F8FAFC` | Slate-50 dengan garis tepi vertikal `border-r border-slate-200` |
| **Text Heading** | `#0F172A` | Slate-900 untuk judul dan data primer |
| **Text Secondary** | `#64748B` | Slate-500 untuk subjudul dan label keterangan |

### Triase Medis (KGD)
* **Merah (Emergent)**: Teks/Ikon `#DC2626`, BG `#FEF2F2`, Border `#FECACA`
* **Kuning (Urgent)**: Teks/Ikon `#D97706`, BG `#FFFBEB`, Border `#FDE68A`
* **Hijau (Non-Urgent)**: Teks/Ikon `#16A34A`, BG `#F0FDF4`, Border `#BBF7D0`
* **Hitam (Meninggal)**: Teks/Ikon `#1E293B`, BG `#F1F5F9`, Border `#CBD5E1`

---

## 5. Ringkasan 14 Tabel Basis Data

1. `users` — Akun multi-role (admin, dosen, mahasiswa) + path paraf digital.
2. `courses` — Stase kurikulum (KGD: `WAT5.31.24`, KDM: `WAT6.07.24`, KMB: `WAT5.24.24`).
3. `student_groups` — Pemetaan kelompok bimbingan mahasiswa, stase, dan dosen pembimbing.
4. `master_sdki` — Standar Diagnosis Keperawatan Indonesia + major/minor signs (JSON).
5. `master_slki` — Standar Luaran Keperawatan Indonesia + indikator luaran (JSON).
6. `master_siki` — Standar Intervensi Keperawatan Indonesia + tindakan observasi/terapeutik/edukasi/kolaborasi (JSON).
7. `master_spo_procedures` — Katalog tindakan klinis mandiri per target Sub-CPMK RPS.
8. `care_sessions` — Sesi asuhan kasus pasien (UUID, identitas pasien, triase, status dokumen).
9. `care_session_assessments` — Data pengkajian spesifik stase (`kgd`, `kdm`, `kmb`) format native JSON.
10. `nursing_care_plans` — Rencana 3S: data DS/DO, etiologi, perumusan diagnosa, prioritas.
11. `care_procedure_logs` — Checklist pelaksanaan tindakan SPO + stempel e-paraf dosen.
12. `vital_sign_monitorings` — Pemantauan TTV time-series (TD, Nadi, RR, Suhu, SpO2, GCS).
13. `evaluations_and_handovers` — Catatan evaluasi SOAP dan serah terima format SBAR (JSON).
14. `session_reviews` — Evaluasi dosen, catatan revisi, skor rubrik Sub-CPMK (0-100), dan tanda tangan digital.

---

## 6. Siklus Hidup Dokumen (Document Lifecycle)

```text
[1. DRAFT] ──────────► Mahasiswa mengisi pengkajian klinis & checklist SPO (Auto-save)
     │
 (Submit)
     ▼
[2. SUBMITTED] ──────► Berkas terkunci bagi mhs; Masuk antrean telaah Dosen/CI
     │
 ┌───┴───────────────────────────────────┐
 │ (Perlu koreksi DS/DO atau tindakan)   │ (Temuan valid & tindakan terverifikasi)
 ▼                                       ▼
[3. NEED_REVISION]                  [4. APPROVED_GRADED]
 │                                       │
 │ Mhs merevisi field tertentu           │ Dosen isi skor rubrik & bubuhkan e-paraf
 └───────► (Submit ulang)                │ Berkas terkunci permanen (Immutable)
                                         ▼
                                    [5. ARCHIVED]
                                         │
                                    Unduh PDF Resmi 1:1 Poltekkes Riau
```

---

## 7. Perintah Esensial (Commands Cheat-Sheet)

### Backend (`e-askep-backend`)
```bash
# Pindah ke direktori backend
cd C:/laragon/www/e-askep-riau/e-askep-backend

# Setup awal
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm install && npm run build

# Menjalankan server dev
php artisan serve --host=0.0.0.0 --port=8000
npm run dev

# Testing otomatis
php artisan test
php artisan test --filter=CareSessionTest
```

### Mobile Client (`e-askep-mobile`)
```bash
# Pindah ke direktori mobile
cd C:/laragon/www/e-askep-riau/e-askep-mobile

# Setup awal
flutter pub get
flutter packages pub run build_runner build --delete-conflicting-outputs

# Menjalankan di emulator / HP fisik
flutter run
flutter test
```

---

## 8. Panduan Kerja AI Agent & Engineering Rules

1. **Selalu rujuk PRD resmi**: Spesifikasi lengkap ada di [`.claude/plan/e_Askep_Poltekkes_Kemenkes_Riau_PRD_Final_Design.md`](file:///C:/laragon/www/e-askep-riau/.claude/plan/e_Askep_Poltekkes_Kemenkes_Riau_PRD_Final_Design.md).
2. **Kepatuhan Rules**:
   * Standar controller, API envelope, dan form request di [`.claude/rules/backend-standards.md`](file:///C:/laragon/www/e-askep-riau/.claude/rules/backend-standards.md).
   * Standar Shadcn UI, palet hijau toska, dan floating card di [`.claude/rules/frontend-standards.md`](file:///C:/laragon/www/e-askep-riau/.claude/rules/frontend-standards.md).
   * Standar skema 14 tabel & payload JSON di [`.claude/rules/database-standards.md`](file:///C:/laragon/www/e-askep-riau/.claude/rules/database-standards.md).
   * Standar keamanan medis & audit trail di [`.claude/rules/security-standards.md`](file:///C:/laragon/www/e-askep-riau/.claude/rules/security-standards.md).
3. **Pemanfaatan Skills**:
   * Pengkajian KGD, KDM, KMB: baca [`.claude/skills/pengkajian-klinis.md`](file:///C:/laragon/www/e-askep-riau/.claude/skills/pengkajian-klinis.md).
   * Penalaran SDKI/SLKI/SIKI: baca [`.claude/skills/integrasi-3s.md`](file:///C:/laragon/www/e-askep-riau/.claude/skills/integrasi-3s.md).
   * Review CI & e-paraf: baca [`.claude/skills/eparaf-dan-penilaian.md`](file:///C:/laragon/www/e-askep-riau/.claude/skills/eparaf-dan-penilaian.md).
4. **Verifikasi Fitur**: Selalu lakukan pengujian sesuai [`.claude/testing/TESTING-PER-FITUR.md`](file:///C:/laragon/www/e-askep-riau/.claude/testing/TESTING-PER-FITUR.md) sebelum menandai sprint selesai.
