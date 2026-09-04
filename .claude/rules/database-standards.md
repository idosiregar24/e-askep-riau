# Database Standards — MySQL 8.0+ DDL, Hybrid JSON & Eloquent
## e-Askep Poltekkes Kemenkes Riau

---

## 1. Strategi Pemodelan Data: Hibrida Relasional + Native JSON

Sistem **e-Askep Poltekkes Riau** mengadopsi pendekatan pemodelan data hibrida:
1. **Normalisasi Relasional Murni (1NF - 3NF)**:
   * Digunakan untuk entitas transaksi utama, relasi otorisasi, master kurikulum, dan integritas finansial/nilai.
   * Meliputi: `users`, `courses`, `student_groups`, `master_sdki`, `master_slki`, `master_siki`, `master_spo_procedures`, `care_sessions`, `care_procedure_logs`, `vital_sign_monitorings`, dan `session_reviews`.
2. **Kolom Native `JSON`**:
   * Digunakan untuk instrumen pengkajian klinis yang memiliki variasi field luas antar stase (KGD dengan ABCDE/AMPLE, KDM dengan 9 Henderson & 6 Benar Obat, KMB dengan 9 Sistem Organ & Penunjang), serta rincian rubrik kuantitatif.
   * Meliputi: `care_session_assessments.assessment_payload`, `evaluations_and_handovers.payload`, `session_reviews.rubric_scores`, dan tanda/gejala/tindakan pada master 3S.

---

## 2. Struktur 14 Tabel Inti (DDL Acuan)

```sql
-- 1. PENGGUNA & OTENTIKASI
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

-- 2. MATA KULIAH / STASE KURIKULUM
CREATE TABLE courses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL, -- WAT5.31.24 (KGD), WAT6.07.24 (KDM), WAT5.24.24 (KMB)
    name VARCHAR(255) NOT NULL,
    program_study VARCHAR(100) NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. KELOMPOK PRAKTIK & BIMBINGAN
CREATE TABLE student_groups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id BIGINT UNSIGNED NOT NULL,
    mentor_dosen_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    group_name VARCHAR(50) NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (mentor_dosen_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_student_mentor (student_id, mentor_dosen_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. MASTER SDKI
CREATE TABLE master_sdki (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    sub_category VARCHAR(100) NOT NULL,
    major_signs JSON NULL,
    minor_signs JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_sdki_code_cat (code, category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. MASTER SLKI
CREATE TABLE master_slki (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    indicators JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. MASTER SIKI
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
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_course_subcpmk (course_id, sub_cpmk_reference)
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
    FOREIGN KEY (mentor_dosen_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_student_status (student_id, status),
    INDEX idx_mentor_status (mentor_dosen_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. PENGKAJIAN KLINIS ADAPTIF (JSON)
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

-- 11. LOGBOOK TINDAKAN SPO & E-PARAF
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
    FOREIGN KEY (verified_by_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_session_verified (care_session_id, is_verified)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. PEMANTAUAN TTV BERKALA
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

## 3. Ketentuan Indexing & Kinerja Kueri (<200 ms)

1. Semua Foreign Key wajib memiliki index pendukung (otomatis dibuat oleh MySQL untuk FK, tetapi index gabungan seperti `idx_student_status` harus dideklarasikan eksplisit).
2. Kolom kode unik (`code`, `uuid`, `nim_nip`) menggunakan indeks B-Tree default.
3. Kueri pencarian 3S (SDKI/SLKI/SIKI) wajib menyertakan filter kategori untuk pemanfaatan index secara optimal.

---

## 4. Standar Eloquent Model & Casting

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CareSession extends Model
{
    protected $fillable = [
        'uuid',
        'student_id',
        'course_id',
        'mentor_dosen_id',
        'patient_name',
        'medical_record_no',
        'age',
        'gender',
        'triage_category',
        'status',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at'  => 'datetime',
    ];

    public function assessment(): HasOne
    {
        return $this->hasOne(CareSessionAssessment::class);
    }

    public function carePlans(): HasMany
    {
        return $this->hasMany(NursingCarePlan::class)->orderBy('priority_order');
    }

    public function procedureLogs(): HasMany
    {
        return $this->hasMany(CareProcedureLog::class);
    }

    public function vitalSigns(): HasMany
    {
        return $this->hasMany(VitalSignMonitoring::class)->orderBy('recorded_at');
    }

    public function review(): HasOne
    {
        return $this->hasOne(SessionReview::class);
    }
}
```
