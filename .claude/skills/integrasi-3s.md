# Skill: Clinical Reasoning Engine & Integrasi 3S PPNI (SDKI, SLKI, SIKI)
## e-Askep Poltekkes Kemenkes Riau

> **Kapan Digunakan**: Dibaca saat mengimplementasikan fitur analisa data klinis, formulasi diagnosa keperawatan, penentuan luaran, dan intervensi berbasis standar taksonomi 3S PPNI.

---

## 1. Konsep Dasar Taksonomi 3S PPNI

Sistem e-Askep Poltekkes Riau menerapkan integrasi penuh tiga standar resmi PPNI:
1. **SDKI (Standar Diagnosis Keperawatan Indonesia)**:
   * Kode: `D.0001` s/d `D.0149` (Kategori: Fisiologis, Psikologis, Perilaku, Relasional, Lingkungan).
   * Memiliki Gejala/Tanda Mayor (wajib ditemukan minimal 80%) dan Gejala/Tanda Minor.
2. **SLKI (Standar Luaran Keperawatan Indonesia)**:
   * Kode: `L.xxxxx` (misal: `L.01004` Bersihan Jalan Napas Meningkat).
   * Memiliki ekspektasi (*Meningkat*, *Menurun*, atau *Membaik*) dan indikator penilaian bernilai skala 1–5.
3. **SIKI (Standar Intervensi Keperawatan Indonesia)**:
   * Kode: `I.xxxxx` (misal: `I.01011` Manajemen Jalan Napas).
   * Dikelompokkan ke dalam 4 pilar tindakan: **Observasi**, **Terapeutik**, **Edukasi**, dan **Kolaborasi**.

---

## 2. Alur Penalaran Klinis (*Clinical Reasoning Workflow*)

```text
  [1. PENGELOMPOKAN DATA]  ──► Mahasiswa input DS (Subjektif) & DO (Objektif)
            │
            ▼
  [2. PENCARIAN SDKI]     ──► Autocomplete instan mencari kode/judul diagnosa SDKI
            │                  Sistem membandingkan temuan DO/DS dengan Major Signs
            ▼
  [3. TENTUKAN ETIOLOGI]  ──► Input penyebab klinis yang mendasari (Etiology)
            │
            ▼
  [4. PILIH LUARAN SLKI]  ──► Tautkan kriteria hasil target dan skor baseline ke target
            │
            ▼
  [5. PILIH SIKI]         ──► Pilih tindakan: Observasi, Terapeutik, Edukasi, Kolaborasi
            │
            ▼
  [6. RANKING PRIORITAS]  ──► Urutkan prioritas diagnosa (Prioritas 1 = Masalah Nyawa/Primer)
```

---

## 3. Struktur Tabel `nursing_care_plans`

```sql
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
);
```

---

## 4. Format Payload Respons API Pencarian 3S

Endpoint: `GET /api/v1/master/sdki-slki-siki?category=fisiologis&search=bersihan`

```json
{
  "success": true,
  "data": [
    {
      "id": 12,
      "code": "D.0001",
      "title": "Bersihan Jalan Napas Tidak Efektif",
      "category": "Fisiologis",
      "sub_category": "Respirasi",
      "major_signs": {
        "subjective": [],
        "objective": ["Batuk tidak efektif", "Tidak mampu batuk", "Sputum berlebih", "Mengi/Wheezing/Ronkhi basah"]
      },
      "linked_slki": [
        {
          "id": 5,
          "code": "L.01001",
          "title": "Bersihan Jalan Napas",
          "expectation": "Meningkat"
        }
      ],
      "linked_siki": [
        {
          "id": 8,
          "code": "I.01011",
          "title": "Manajemen Jalan Napas",
          "actions": {
            "observasi": ["Monitor pola napas", "Monitor bunyi napas tambahan", "Monitor sputum"],
            "terapeutik": ["Pertahankan kepatenan jalan napas", "Posisikan semi-fowler atau fowler", "Berikan oksigen jika perlu"],
            "edukasi": ["Anjurkan asupan cairan 2000 ml/hari jika tidak kontraindikasi", "Ajarkan teknik batuk efektif"],
            "kolaborasi": ["Kolaborasi pemberian bronkodilator, ekspektoran, mukolitik jika perlu"]
          }
        }
      ]
    }
  ]
}
```

---

## 5. Aturan Bisnis & Validasi Klinis
1. Setiap sesi asuhan minimal memiliki **1 rencana asuhan keperawatan** dengan diagnosa primer yang valid sebelum berkas dapat di-*submit*.
2. Pada stase KGD, diagnosa yang berhubungan dengan ancaman jalan napas (*Airway*), pernapasan (*Breathing*), dan sirkulasi (*Circulation*) wajib ditempatkan pada **Prioritas 1 dan 2**.
