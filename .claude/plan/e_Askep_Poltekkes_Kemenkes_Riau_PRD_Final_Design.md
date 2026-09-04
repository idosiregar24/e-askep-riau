# SYSTEM SPECIFICATION & PRODUCT REQUIREMENT DOCUMENT (PRD)
## e-Askep Poltekkes Kemenkes Riau
### Sistem Digitalisasi Asuhan Keperawatan & Logbook Praktikum Klinis Terintegrasi

---

## 0. EXECUTIVE SYSTEM SPECIFICATION & TECH STACK (AI AGENT & DEVELOPER DIRECTIVE)

> **PETUNJUK MUTLAK UNTUK AI AGENT & SOFTWARE ENGINEER:**  
> Sistem ini mengadopsi arsitektur **Hybrid Monolith + Mobile API-First**. Backend Laravel 11 menangani web interface (Admin & Dosen) serta RESTful API untuk Mobile Client (Flutter). Seluruh antarmuka web dan mobile wajib mengusung tema **Clinical Clean Minimalism** menggunakan palet putih bersih (*white background*) dengan aksen warna medis terstandar (*clinical slate & emerald*), serta komponen UI berbasis filosofi **Shadcn UI**.

### 0.1 Core Tech Stack
* **Backend Web & REST API:** **Laravel 11 (PHP 8.3+)**
  * **Web Portal (Admin & Dosen):** Laravel Blade + Livewire v3 / Alpine.js dengan komponen bergaya **Shadcn UI** (diimplementasikan via Tailwind CSS + Radix-like accessible markup / MaryUI / Flux UI).
  * **API Engine:** Laravel Sanctum (Token-based Stateless Authentication untuk mobile).
  * **Otorisasi & RBAC:** `spatie/laravel-permission` (Admin, Dosen/CI, Mahasiswa).
  * **PDF Engine:** `barryvdh/laravel-snappy` (menggunakan binary `wkhtmltopdf`) untuk render dokumen cetak presisi 1:1 format fisik Poltekkes Riau.
* **Mobile Client (Android):** **Flutter 3.x (Dart)**
  * **Platform Target:** Android Smartphone & Tablet.
  * **State Management:** BLoC (`flutter_bloc`) / Cubit.
  * **Offline-First Storage:** `drift` (SQLite) / `isar` untuk mekanisme draf lokal anti-kehilangan data di bangsal.
  * **Network Client:** `dio` dengan interceptor token Sanctum dan auto-sync.
  * **Digital Signature Pad:** `signature` package (ekspor bitmap transparan untuk paraf digital).
* **Database Management System:** **MySQL 8.0+**
  * **Storage Engine:** InnoDB, `utf8mb4_unicode_ci`.
  * **Modeling Strategy:** Relasional normalisasi untuk tabel inti, dikombinasikan dengan kolom tipe native `JSON` untuk payload fleksibel form klinis (ABCDE, 9 Henderson, Head-to-Toe, dan SBAR/SOAP).

### 0.2 Design System & Visual Reference: Poltekkes Kemenkes Riau Palette (Shadcn Minimalist)

> **ACUAN VISUAL (BERDASARKAN REFERENSI DESAIN UI):**  
> Mengadopsi tata letak modern seperti referensi: **Floating Card Frame** dengan sudut melengkung halus (*rounded-2xl*), bilah sisi kiri (*left sidebar*) modular bertingkat, kanvas tengah lapang putih bersih (*content surface*), dan kartu status interaktif (*status chips & cards*). Skema warna disesuaikan 100% dengan **identitas resmi Poltekkes Kemenkes Riau**.

* **Palet Warna Resmi Poltekkes Kemenkes Riau & Medis:**
  * **Primary / Brand (Hijau Toska Kemenkes):** `#008D88` (Hover: `#00736F`, Light Tint: `#E6F5F4`) — Digunakan pada tombol aksi utama, badge aktif, dan header modul.
  * **Secondary / Accent (Kuning Emas Poltekkes):** `#EAB308` (Deep: `#CA8A04`, Soft Pill: `#FEF9C3`) — Digunakan untuk aksen penilaian, catatan evaluasi, dan highlight penting.
  * **Base Canvas Background:** `#F1F5F9` (Slate-100 lembut — memberikan efek kartu mengambang/floating).
  * **Main Card / Surface Background:** `#FFFFFF` (Pure White, latar putih bersih tanpa gradasi gelap).
  * **Sidebar Surface:** `#FAFAFA` / `#F8FAFC` (Slate-50 dengan garis batas tipis vertikal `border-r border-slate-200`).
  * **Typography Color:**
    * *Heading & Primary Data:* `#0F172A` (Slate-900, ketajaman baca maksimal).
    * *Secondary / Captions:* `#64748B` (Slate-500).
    * *Sidebar Menu Default:* `#475569` (Slate-600).
    * *Sidebar Menu Active:* `#008D88` dengan background kapsul `#E6F5F4` (*rounded-full pill*).
  * **Triase Kegawatdaruratan Semantik (Wajib Standar Medis):**
    * Merah (*Emergent*): `#DC2626` (Soft BG: `#FEF2F2`, Border: `#FECACA`)
    * Kuning (*Urgent*): `#D97706` (Soft BG: `#FFFBEB`, Border: `#FDE68A`)
    * Hijau (*Non-Urgent*): `#16A34A` (Soft BG: `#F0FDF4`, Border: `#BBF7D0`)
    * Hitam (*Meninggal*): `#1E293B` (Soft BG: `#F1F5F9`, Border: `#CBD5E1`)

* **Karakteristik Komponen UI (Shadcn Style):**
  * **Left Sidebar:** Modular dengan pengelompokan menu (*Main Menu, Settings, Profile Card* di bagian bawah). Menu aktif menggunakan gaya *pill tab* putih melayang dengan teks hijau Kemenkes dan bayangan sangat lembut.
  * **Header & Breadcrumbs:** Minimalis tipis di atas kanvas kerja (`Mahasiswa > Kasus KGD > Triase`).
  * **Cards & Containers:** Kartu bersudut *rounded-xl*, outline garis halus 1px (`border border-slate-200/80`), dan bayangan mikro (`shadow-xs` / `shadow-sm`).
  * **Filter & Action Chips:** Tombol filter bulan/stase berbentuk kapsul pipih (*soft rounded-full pill*) dengan status aktif warna gelap/toska.

### 0.3 Setup & Environment Architecture (Laragon Workspace)

Sistem dirancang di dalam direktori root Laragon sebagai berikut:

```text
C:/laragon/www/e-askep-riau/
├── e-askep-backend/               <--- Laravel 11 (Web Admin/Dosen + REST API)
└── e-askep-mobile/                <--- Flutter 3.x Android Application
```

#### 1. Setup Backend (`e-askep-backend`)
* **Virtual Host Laragon:**
  * Document Root: `C:/laragon/www/e-askep-riau/e-askep-backend/public`
  * Domain Lokal: `http://e-askep-backend.test` atau dijalankan via `php artisan serve --host=0.0.0.0 --port=8000`.
* **Konfigurasi `.env` Kunci:**
  ```env
  APP_NAME="e-Askep Poltekkes Riau"
  APP_ENV=local
  APP_KEY=base64:...
  APP_URL=http://e-askep-backend.test

  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=e_askep_db
  DB_USERNAME=root
  DB_PASSWORD=

  SANCTUM_STATEFUL_DOMAINS=e-askep-backend.test
  SESSION_DRIVER=database
  QUEUE_CONNECTION=database
  ```
* **Langkah Inisiasi Backend:**
  ```bash
  cd C:/laragon/www/e-askep-riau/e-askep-backend
  composer install
  cp .env.example .env
  php artisan key:generate
  php artisan migrate --seed
  npm install && npm run build
  php artisan serve --host=0.0.0.0 --port=8000
  ```

#### 2. Setup Mobile Frontend (`e-askep-mobile`)
* **Konfigurasi Base URL (`lib/core/constants/api_endpoints.dart`):**
  * Android Emulator: `http://10.0.2.2:8000/api/v1`
  * HP Fisik (USB Debugging / Wi-Fi): `http://192.168.x.x:8000/api/v1` (IP laptop Laragon)
* **Langkah Inisiasi Mobile App:**
  ```bash
  cd C:/laragon/www/e-askep-riau/e-askep-mobile
  flutter pub get
  flutter run
  ```

---

## 1. Metadata Dokumen & Latar Belakang
* **Nama Produk:** **e-Askep Poltekkes Kemenkes Riau**
* **Nama Resmi Sistem:** Sistem Informasi Asuhan Keperawatan & Logbook Praktikum Klinis Terintegrasi (*e-Askep Riau*)
* **Tagline:** *Presisi Pengkajian, Tertib Prosedur, dan Evaluasi Klinis Tanpa Kertas.*
* **Versi Dokumen:** 2.2.0
* **Status Dokumen:** Approved Technical Architecture
* **Target Pengguna:** 
  1. **Mahasiswa D-III & Sarjana Terapan Keperawatan:** Pengisi temuan pengkajian, perumus diagnosa SDKI, pelaksana prosedur SPO, dan pembuat laporan SOAP/SBAR.
  2. **Dosen / Instruktur Klinis / CI (Clinical Instructor):** Penguji, penilai rubrik Sub-CPMK, pemberi catatan revisi, dan validator checklist tindakan melalui e-paraf.
  3. **Administrator Akademik / Program Studi Poltekkes Kemenkes Riau:** Pengelola master data kurikulum (RPS, Sub-CPMK), kamus 3S PPNI, akun pengguna, dan pemetaan kelompok bimbingan.

### 1.1 Latar Belakang & Acuan Kurikulum
Dokumentasi asuhan keperawatan dan logbook praktikum klinis/laboratorium di Poltekkes Kemenkes Riau saat ini menggunakan instrumen berbasis dokumen cetak (*paper-based*). Sistem ini mendigitalkan tiga instrumen kurikulum berbasis Rencana Pembelajaran Semester (RPS):
1. **Keperawatan Gawat Darurat (KGD):** Mengacu pada RPS WAT5.31.24 Kelas D. Berfokus pada klasifikasi Triase (ESI/START/CTAS), Survei Primer ($ABCDE$), Survei Sekunder ($AMPLE$ & Head-to-Toe), checklist tindakan live-saving Sub-CPMK 2–13, analisa data SDKI/SLKI/SIKI, pemantauan tanda vital berkala dinamis, dan handover format SBAR.
2. **Kebutuhan Dasar Manusia (KDM):** Mengacu pada RPS WAT6.07.24 Program Studi Sarjana Terapan Keperawatan. Berfokus pada 9 domain kebutuhan dasar Virginia Henderson/Maslow, checklist kepatuhan SPO teknis mandiri Sub-CPMK 1–9, prosedur prinsip 6 Benar Pemberian Obat Sub-CPMK 2, serta catatan perkembangan SOAP per pertemuan.
3. **Keperawatan Medikal Bedah (KMB):** Mengacu pada RPS WAT5.24.24 Program Studi D-III Keperawatan. Berfokus pada pengkajian multi-sistem organ tubuh, riwayat keluarga/genogram, pemeriksaan diagnostik/penunjang (EKG, AGD, Ro Thorax, Lab), asuhan perioperatif (pra, intra, dan pasca bedah), dan siklus asuhan keperawatan komprehensif.

### 1.2 Masalah Saat Ini (*Problem Statements*)
* **Redundansi Tulis Tangan:** Mahasiswa menghabiskan waktu lama menulis ulang taksonomi standar (SDKI, SLKI, SIKI) secara manual di lembaran kertas instrumen.
* **Keterlambatan Validasi Paraf:** Lembar checklist prosedur SPO praktikum sering tertunda divalidasi pembimbing karena ketergantungan pada tanda tangan fisik di bangsal atau lab.
* **Risiko Dokumen Rusak & Hilang:** Berkas fisik rentan tercecer, menyulitkan rekapitulasi portofolio kelulusan Sub-CPMK semester berjalan.
* **Inkonsistensi Analisis Data:** Data Subjektif/Objektif (DS/DO) sering kali tidak sinkron dengan diagnosa dan rencana intervensi yang ditegakkan tanpa adanya validasi sistemik.

---

## 2. Matriks Peran & Hak Akses (Role-Based Access Control / RBAC)

| Modul / Kemampuan Sistem | Admin (Web) | Mahasiswa (App & Web) | Dosen / CI (App & Web) |
| :--- | :---: | :---: | :---: |
| **Manajemen Master Kurikulum & Akun** (RPS, Sub-CPMK, Akun, Kelompok Praktik) | **CRUD Penuh** | Hanya Baca (*Read-Only*) | Hanya Baca (*Read-Only*) |
| **Manajemen Master 3S & SPO** (SDKI, SLKI, SIKI, Katalog Prosedur Tindakan) | **CRUD Penuh** | Hanya Baca (*Read-Only*) | Hanya Baca (*Read-Only*) |
| **Pengisian Formulir Askep** (KGD / KDM / KMB: Anamnesis, Fisik, DS/DO) | Audit & Pantau | **Create, Read, Update (Draft), Submit** | Read, Beri Catatan Revisi |
| **Checklist Prosedur Tindakan SPO** | Monitoring | Centang Checklist Mandiri + Waktu | **Verifikasi E-Paraf (Approve/Reject)** |
| **Monitoring TTV & Reassessment** | Audit Trail | Input berkala time-series | Evaluasi & Verifikasi |
| **Dokumentasi SBAR & SOAP** | Monitoring | Penyusunan draf SBAR/SOAP | Evaluasi & Feedback |
| **Penilaian Rubrik Sub-CPMK** | Rekap & Ekspor Nilai | Lihat Nilai & Catatan Pembimbing | **Input Nilai Rubrik, Catatan, Sign-off** |
| **Ekspor Dokumen Resmi (PDF)** | Cetak Arsip Prodi | Unduh Berkas Final (Terkunci) | Unduh Berkas Mahasiswa Bimbingan |

---

## 3. Siklus Hidup Dokumen (*Document Lifecycle*)

```text
       [1. DRAFT]  <────── Mahasiswa mengisi pengkajian & checklist SPO (Auto-save)
           │
      (Klik Submit)
           ▼
 [2. SUBMITTED / IN REVIEW] <── Berkas terkunci bagi mhs; Masuk antrean review Dosen/CI
           │
    ┌──────┴─────────────────────────────────┐
    │ (Ada ketidaksesuaian temuan/analisa)   │ (Temuan valid & tindakan terverifikasi)
    ▼                                        ▼
[3. NEED REVISION]                  [4. APPROVED & GRADED]
    │                                        │
    │ Catatan revisi dikirim ke mhs          │ Dosen input skor rubrik & bubuhkan tanda tangan
    │ Mhs menyunting field yang diminta      │ Berkas terkunci permanen (Immutable)
    └────────► (Submit Ulang)                ▼
                                      [5. ARCHIVED]
                                             │
                                     Generate PDF Resmi
```

---

## 4. Kebutuhan Fungsional (Functional Requirements)

### FR-01: Modul Web Admin & Kurikulum (Clean Shadcn UI)
* **FR-01.1:** Otentikasi aman JWT/Sanctum untuk mobile dan sesi web untuk portal web.
* **FR-01.2:** Manajemen kelompok bimbingan: Admin memetakan mahasiswa, stase aktif (KGD/KDM/KMB), dan dosen pembimbing.
* **FR-01.3:** Manajemen Master 3S PPNI: Master SDKI, SLKI, dan SIKI berbasis tabel interaktif putih minimalis dengan pencarian instan dan pagination.
* **FR-01.4:** Manajemen Master SPO: Pengelompokan katalog prosedur klinis per target Sub-CPMK.

### FR-02: Workspace Mahasiswa (Mobile Flutter & Web View)
* **FR-02.1 (Multi-Step Form Wizard):** Antarmuka input data bertahap dengan auto-save lokal.
* **FR-02.2 (Stase KGD):** Triase visual (Merah, Kuning, Hijau, Hitam), Survei Primer ABCDE, AMPLE & Head-to-Toe, checklist tindakan Sub-CPMK 2–13, tabel pemantauan TTV berkala dinamis, dan handover format SBAR.
* **FR-02.3 (Stase KDM):** Pengkajian komprehensif 9 Kebutuhan Henderson, checklist SPO Sub-CPMK 1–9, penerapan 6 Benar Obat, evaluasi SOAP.
* **FR-02.4 (Stase KMB):** Anamnesis komprehensif (keluhan, PQRST, riwayat keluarga/genogram), pengkajian 9 sistem tubuh, data penunjang EKG/Lab/Radiologi, dan modul asuhan perioperatif.
* **FR-02.5 (Clinical Reasoning / 3S Engine):** Pengelompokan Data Subjektif (DS) dan Objektif (DO), autocomplete pencarian diagnosa SDKI terhubung ke SLKI dan SIKI.

### FR-03: Workspace Dosen / CI (Web Desktop & Mobile Flutter)
* **FR-03.1:** Antrean telaah kasus mahasiswa bimbingan dengan filter: *Perlu Paraf Tindakan*, *Perlu Penilaian Askep*, dan *Revisi Berjalan*.
* **FR-03.2:** Fitur *One-Click Batch E-Paraf* untuk memvalidasi seluruh tindakan SPO yang dilakukan mahasiswa via web canvas atau sentuhan layar HP.
* **FR-03.3:** Fitur *Inline Commenting*: Dosen dapat menyematkan catatan perbaikan pada baris data pengkajian yang keliru.
* **FR-03.4:** Formulir Penilaian Terintegrasi: Input skor rubrik kuantitatif Sub-CPMK (0–100) dan pembubuhan tanda tangan digital akhir.

### FR-04: Modul Pelaporan & Ekspor Dokumen Resmi
* **FR-04.1:** Generator dokumen PDF resmi dengan tata letak (*layout*) yang identik 100% dengan format baku Poltekkes Kemenkes Riau.
* **FR-04.2:** Penyematan metadata keabsahan: Waktu persetujuan, NIP/NIDN dosen penilai, dan kode QR verifikasi berkas.

---

## 5. Perancangan Skema Basis Data (MySQL 8.0+ DDL)

```sql
-- 1. TABEL PENGGUNA & OTENTIKASI
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    nim_nip VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'dosen', 'mahasiswa') NOT NULL,
    signature_path VARCHAR(255) NULL,
    phone_number VARCHAR(20) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TABEL MATA KULIAH / STASE KURIKULUM
CREATE TABLE courses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL, -- WAT5.31.24, WAT6.07.24, WAT5.24.24
    name VARCHAR(255) NOT NULL,       -- KGD, KDM, KMB
    program_study VARCHAR(100) NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. RELASI KELOMPOK PRAKTIK & BIMBINGAN
CREATE TABLE student_groups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id BIGINT UNSIGNED NOT NULL,
    mentor_dosen_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    group_name VARCHAR(50) NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (mentor_dosen_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. MASTER SDKI (Standar Diagnosis Keperawatan Indonesia)
CREATE TABLE master_sdki (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    sub_category VARCHAR(100) NOT NULL,
    major_signs JSON NULL,
    minor_signs JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. MASTER SLKI (Standar Luaran Keperawatan Indonesia)
CREATE TABLE master_slki (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    indicators JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. MASTER SIKI (Standar Intervensi Keperawatan Indonesia)
CREATE TABLE master_siki (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    actions JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. MASTER KATALOG PROSEDUR SPO PRAKTIKUM
CREATE TABLE master_spo_procedures (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id BIGINT UNSIGNED NOT NULL,
    sub_cpmk_reference VARCHAR(50) NOT NULL,
    domain_category VARCHAR(100) NOT NULL,
    procedure_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. ENTITAS UTAMA SESI ASUHAN KASUS
CREATE TABLE care_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    mentor_dosen_id BIGINT UNSIGNED NOT NULL,
    patient_name VARCHAR(255) NOT NULL,
    medical_record_no VARCHAR(100) NULL,
    age VARCHAR(50) NOT NULL,
    gender ENUM('L', 'P') NOT NULL,
    triage_category ENUM('merah', 'kuning', 'hijau', 'hitam') NULL,
    status ENUM('draft', 'submitted', 'need_revision', 'approved_graded', 'archived') DEFAULT 'draft',
    submitted_at DATETIME NULL,
    approved_at DATETIME NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (mentor_dosen_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. DATA PENGKAJIAN KLINIS ADAPTIF (JSON-based)
CREATE TABLE care_session_assessments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    care_session_id BIGINT UNSIGNED NOT NULL,
    stage_type ENUM('kgd', 'kdm', 'kmb') NOT NULL,
    assessment_payload JSON NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (care_session_id) REFERENCES care_sessions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. ANALISA DATA & PERENCANAAN 3S
CREATE TABLE nursing_care_plans (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    care_session_id BIGINT UNSIGNED NOT NULL,
    sdki_id BIGINT UNSIGNED NULL,
    slki_id BIGINT UNSIGNED NULL,
    siki_id BIGINT UNSIGNED NULL,
    subjective_data TEXT NOT NULL,
    objective_data TEXT NOT NULL,
    etiology TEXT NOT NULL,
    custom_outcome_targets TEXT NULL,
    custom_interventions TEXT NULL,
    priority_order INT DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (care_session_id) REFERENCES care_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (sdki_id) REFERENCES master_sdki(id) ON DELETE SET NULL,
    FOREIGN KEY (slki_id) REFERENCES master_slki(id) ON DELETE SET NULL,
    FOREIGN KEY (siki_id) REFERENCES master_siki(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. LOGBOOK CHECKLIST TINDAKAN & E-PARAF
CREATE TABLE care_procedure_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    care_session_id BIGINT UNSIGNED NOT NULL,
    procedure_id BIGINT UNSIGNED NOT NULL,
    is_performed BOOLEAN DEFAULT FALSE,
    performed_at DATETIME NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    verified_by_id BIGINT UNSIGNED NULL,
    verified_at DATETIME NULL,
    notes TEXT NULL,
    FOREIGN KEY (care_session_id) REFERENCES care_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (procedure_id) REFERENCES master_spo_procedures(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. PEMANTAUAN TTV BERKALA (REASSESSMENT)
CREATE TABLE vital_sign_monitorings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    care_session_id BIGINT UNSIGNED NOT NULL,
    recorded_at TIME NOT NULL,
    blood_pressure VARCHAR(30) NULL,
    heart_rate VARCHAR(30) NULL,
    respiratory_rate VARCHAR(30) NULL,
    spo2 VARCHAR(20) NULL,
    temperature VARCHAR(20) NULL,
    gcs_score VARCHAR(20) NULL,
    evaluation_notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (care_session_id) REFERENCES care_sessions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. EVALUASI DAN SERAH TERIMA (SBAR & SOAP)
CREATE TABLE evaluations_and_handovers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    care_session_id BIGINT UNSIGNED NOT NULL,
    format_type ENUM('SBAR', 'SOAP') NOT NULL,
    payload JSON NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (care_session_id) REFERENCES care_sessions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. PENILAIAN RUBRIK & CATATAN REVIEW PEMBIMBING
CREATE TABLE session_reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    care_session_id BIGINT UNSIGNED NOT NULL,
    dosen_id BIGINT UNSIGNED NOT NULL,
    revision_notes TEXT NULL,
    rubric_scores JSON NULL,
    final_score DECIMAL(5,2) NULL,
    signature_snapshot_url VARCHAR(255) NULL,
    reviewed_at DATETIME NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (care_session_id) REFERENCES care_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (dosen_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 6. Rencana Kontrak REST API (Laravel 11)

```text
AUTH & AKUN
POST   /api/v1/auth/login                  -> Login pengguna, kembalikan Sanctum Token & data role
GET    /api/v1/auth/me                     -> Cek status otentikasi & profil aktif
POST   /api/v1/auth/logout                 -> Revoke token aktif
POST   /api/v1/profile/signature           -> Upload aset tanda tangan / paraf digital

MASTER DATA
GET    /api/v1/master/sdki-slki-siki       -> Mengambil kamus 3S PPNI (dengan filter & pagination)
GET    /api/v1/master/spo-procedures       -> Mengambil katalog prosedur SPO per stase

SESI ASUHAN (Mahasiswa App/Web)
GET    /api/v1/sessions                    -> Mendapatkan daftar kasus milik user aktif
POST   /api/v1/sessions                    -> Inisiasi kasus baru
GET    /api/v1/sessions/{id}               -> Mengambil data lengkap satu sesi kasus
PUT    /api/v1/sessions/{id}/draft         -> Simpan draf pengkajian klinis & checklist tindakan
POST   /api/v1/sessions/{id}/submit        -> Mengunci dan mengirim berkas ke dosen pembimbing

WORKSPACE DOSEN / CI
GET    /api/v1/ci/submissions              -> Antrean telaah berkas mahasiswa bimbingan
POST   /api/v1/ci/verify-procedures        -> Batch validasi e-paraf checklist tindakan SPO
POST   /api/v1/ci/sessions/{id}/revision   -> Mengembalikan berkas dengan catatan perbaikan
POST   /api/v1/ci/sessions/{id}/grade      -> Input skor rubrik Sub-CPMK & sign-off tanda tangan

DOKUMEN & LAPORAN
GET    /api/v1/sessions/{id}/export-pdf    -> Menghasilkan dan mengunduh berkas PDF resmi
```

---

## 7. Kebutuhan Non-Fungsional (Non-Functional Requirements)

* **NFR-01 (Kinerja & Kecepatan Respons):** Query pencarian master 3S pada basis data MySQL dengan indeks B-Tree harus merespon dalam waktu < 200 ms. Rendering dokumen PDF oleh server Laravel Snappy maksimal 3 detik.
* **NFR-02 (Offline-First Capability):** Aplikasi Flutter wajib mengimplementasikan caching lokal (Isar/Drift). Jika koneksi internet di bangsal/lab terputus, data input pengkajian tersimpan aman di memori lokal dan otomatis tersinkronisasi saat kembali online.
* **NFR-03 (Keamanan & Kerahasiaan Data Medis):**
  * Seluruh komunikasi data menggunakan protokol HTTPS dengan enkripsi TLS 1.3.
  * Token autentikasi disimpan aman di *EncryptedSharedPreferences* (Android).
  * Data sensitif pasien disamarkan sesuai kaidah kerahasiaan rekam medis akademik.
* **NFR-04 (Integritas Dokumen & Audit Trail):**
  * Dokumen dengan status `approved_graded` bersifat *read-only* (immutable) dan terlindungi oleh *hash checksum*.
  * Setiap tindakan verifikasi e-paraf mencatat data audit lengkap (*User ID, IP Address, Timestamp, Device ID*).

---

## 8. Roadmap Pengembangan & Kriteria Keberhasilan

| Sprint | Durasi | Sasaran & Deliverables |
| :---: | :---: | :--- |
| **Sprint 1: Backend & Database Setup** | Minggu 1–3 | Setup Laravel 11 di Laragon, skema MySQL 14 tabel, seeder 3S PPNI & SPO, integrasi tema Shadcn Tailwind. |
| **Sprint 2: Web Portal Admin & Dosen** | Minggu 4–6 | Implementasi halaman web admin & meja review dosen berbasis Shadcn UI bersih minimalis, autentikasi Sanctum. |
| **Sprint 3: Flutter Base & Form Wizard** | Minggu 7–9 | Pembangunan modul wizard KGD, KDM, KMB di Flutter dengan skema warna putih klinis dan fitur auto-draft lokal. |
| **Sprint 4: E-Paraf & Evaluasi Rubrik** | Minggu 10–11 | Fitur telaah side-by-side (Web) & quick-paraf (Mobile), canvas tanda tangan digital, rubrik penilaian Sub-CPMK. |
| **Sprint 5: PDF Engine, UAT & Launch** | Minggu 12 | Integrasi PDF Snappy format 1:1 Poltekkes Riau, pengujian beban MySQL, UAT di Poltekkes Kemenkes Riau. |
