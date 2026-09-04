# Skill: E-Paraf Klinis, Inline Review & Penilaian Rubrik Sub-CPMK
## e-Askep Poltekkes Kemenkes Riau

> **Kapan Digunakan**: Dibaca saat mengimplementasikan fitur workspace Dosen / Clinical Instructor (CI), validasi logbook tindakan SPO, catatan revisi inline, pembubuhan e-paraf, dan evaluasi rubrik kuantitatif.

---

## 1. Peran & Alur Kerja Dosen / Clinical Instructor (CI)

Instruktur Klinis (CI) di rumah sakit dan dosen pembimbing akademik memiliki akses meja telaah (*review desk*) di Web dan Mobile Flutter untuk:
1. **Memvalidasi Checklist Tindakan SPO**: Verifikasi kebenaran tindakan yang dilakukan mahasiswa di ruang rawat/lab.
2. **Review Pengkajian & Rencana Asuhan**: Memberikan umpan balik klinis langsung (*inline comments*).
3. **Pemberian Skor Rubrik Sub-CPMK**: Mengisi evaluasi kuantitatif (skala 0–100) dan menandatangani berkas secara digital.

---

## 2. Alur E-Paraf Batch Logbook Tindakan SPO

```text
Mahasiswa Lakukan Tindakan ──► Centang Checklist di Mobile App (is_performed = true, performed_at)
                                      │
                                      ▼
Antrean CI Mobile/Web      ──► CI Melihat Daftar Tindakan Mahasiswa Bimbingan
                                      │
                                      ▼
Persetujuan Satu Ketukan   ──► CI Klik "Batch Verifikasi E-Paraf"
                                      │
                                      ▼
Penyimpanan Audit Trail    ──► Sistem mencatat verified_by_id, verified_at, IP, & Device ID
```

### Endpoint Verifikasi Tindakan:
* **Endpoint**: `POST /api/v1/ci/verify-procedures`
* **Payload**:
```json
{
  "care_session_id": 15,
  "procedure_ids": [101, 102, 105, 108],
  "verification_status": "approved",
  "notes": "Tindakan pemasangan OPA dan suction dilakukan sesuai prinsip aseptik."
}
```

---

## 3. Fitur Inline Commenting & Siklus Revisi

Jika pembimbing menemukan ketidaksesuaian temuan data objektif dengan diagnosa yang ditegakkan:
1. CI memilih seksi pengkajian yang keliru.
2. Menyematkan catatan revisi (`revision_notes`).
3. Menekan tombol **Kembalikan untuk Revisi** (`POST /api/v1/ci/sessions/{id}/revision`).
4. Status berkas berubah menjadi `need_revision`.
5. Berkas terbuka kembali (*unlocked*) untuk diedit oleh mahasiswa pemilik kasus.

---

## 4. Evaluasi Rubrik Sub-CPMK & Sign-Off Permanen

Setelah seluruh pengkajian valid dan prosedur tindakan terverifikasi, CI mengisi lembar penilaian rubrik Sub-CPMK.

### Struktur Data Nilai Rubrik (`session_reviews.rubric_scores`):
```json
{
  "sub_cpmk_scores": [
    {"sub_cpmk": "Sub-CPMK 2", "name": "Bantuan Hidup Dasar (BHD/RJP)", "score": 88, "weight": 20},
    {"sub_cpmk": "Sub-CPMK 3", "name": "Manajemen Jalan Napas & Oksigenasi", "score": 85, "weight": 20},
    {"sub_cpmk": "Sub-CPMK 4", "name": "Interpretasi EKG Dasar", "score": 80, "weight": 15},
    {"sub_cpmk": "Sub-CPMK 5", "name": "Analisa Data 3S & Dokumentasi SBAR", "score": 90, "weight": 45}
  ],
  "final_score": 86.50,
  "grade_letter": "A",
  "dosen_notes": "Keterampilan klinis sangat baik, pertahankan komunikasi terapeutik."
}
```

### Prosedur Sign-Off & Penguncian Dokumen:
1. Dosen membubuhkan tanda tangan digital pada kanvas tanda tangan (ekspor bitmap transparan PNG).
2. Memanggil endpoint: `POST /api/v1/ci/sessions/{id}/grade`
3. Transaksi membungkus:
   * Simpan data review ke tabel `session_reviews`.
   * Update status berkas `care_sessions` menjadi `approved_graded`.
   * Simpan timestamp `approved_at`.
   * Kunci berkas menjadi **Immutable** (tidak dapat disunting kembali).
