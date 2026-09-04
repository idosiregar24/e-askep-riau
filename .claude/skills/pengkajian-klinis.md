# Skill: Multi-Step Clinical Assessment Wizard (KGD, KDM, KMB)
## e-Askep Poltekkes Kemenkes Riau

> **Kapan Digunakan**: Dibaca saat membangun atau memodifikasi formulir pengkajian klinis, penyimpanan payload JSON pengkajian, dan form wizard untuk tiga stase keperawatan (KGD, KDM, dan KMB) di Web dan Mobile Flutter.

---

## 1. Spesifikasi Instrumen Pengkajian Per Stase

### 1.1 Stase Keperawatan Gawat Darurat (KGD) — RPS WAT5.31.24
* **Klasifikasi Triase**:
  * Merah (*Emergent* / Resusitasi)
  * Kuning (*Urgent*)
  * Hijau (*Non-Urgent*)
  * Hitam (*Death on Arrival* / Meninggal)
* **Survei Primer ($ABCDE$)**:
  * **A (Airway)**: Patensi jalan napas, gurgling, snoring, stridor, fiksasi servikal/collar.
  * **B (Breathing)**: Pola napas, frekuensi, suara napas (vesikuler, ronki, wheezing), deviasi trakea, retraksi dinding dada, saturasi $SpO_2$.
  * **C (Circulation)**: Nadi kualitatif (lemah/kuat), kapiler refill time ($CRT$), perdarahan aktif, warna/akral kulit.
  * **D (Disability)**: Nilai GCS (Eye, Verbal, Motor), respon pupil terhadap cahaya, defisit neurologis.
  * **E (Exposure)**: Cedera menyeluruh, hipotermia, log-roll assessment.
* **Survei Sekunder ($AMPLE$ & Head-to-Toe)**:
  * Allergies, Medications, Past illnesses, Last meal, Events leading to injury.
* **TTV Berkala Dinamis**: Waktu pencatatan time-series (Tekanan Darah, Nadi, RR, $SpO_2$, Suhu, GCS).
* **Handover format SBAR**: Situation, Background, Assessment, Recommendation.

### 1.2 Stase Kebutuhan Dasar Manusia (KDM) — RPS WAT6.07.24
* **9 Domain Kebutuhan Dasar Henderson/Maslow**:
  1. Oksigenasi
  2. Nutrisi dan Cairan
  3. Eliminasi (Urin & Fekal)
  4. Aktivitas dan Istirahat
  5. Kebersihan Diri (*Personal Hygiene*)
  6. Regulasi Suhu Tubuh
  7. Rasa Aman dan Kenyamanan (Skala Nyeri)
  8. Integritas Kulit dan Jaringan
  9. Kebutuhan Psikososial dan Spiritual
* **Prinsip 6 Benar Pemberian Obat (Sub-CPMK 2)**:
  * Benar Pasien, Benar Obat, Benar Dosis, Benar Rute, Benar Waktu, Benar Dokumentasi.
* **Evaluasi Harian**: Format Catatan Perkembangan $SOAP$ (Subjektif, Objektif, Analisa, Perencanaan).

### 1.3 Stase Keperawatan Medikal Bedah (KMB) — RPS WAT5.24.24
* **Anamnesis Komprehensif**:
  * Keluhan Utama, Riwayat Penyakit Sekarang dengan pendekatan $PQRST$ (*Provoking, Quality, Radiation, Severity, Timing*).
  * Riwayat Penyakit Dahulu & Keluarga (termasuk visualisasi/bagan genogram 3 generasi).
* **Pemeriksaan Fisik 9 Sistem Organ**:
  * Sistem Pernapasan, Kardiovaskuler, Pencernaan, Perkemihan, Endokrin, Persarafan, Muskuloskeletal, Integumen, dan Penginderaan.
* **Data Diagnostik & Penunjang**:
  * EKG, Analisa Gas Darah (AGD), Foto Thorax, Pemeriksaan Laboratorium Darah/Urin lengkap.
* **Asuhan Perioperatif**:
  * Pengkajian Pra-Bedah, Intra-Bedah (Checklist Keselamatan Pasien Operasi), dan Pasca-Bedah (Aldrete/Steward Score).

---

## 2. Struktur Penyimpanan Payload JSON (`care_session_assessments`)

```json
{
  "stage": "kgd",
  "triage": {
    "category": "merah",
    "esi_level": 1,
    "arrival_method": "Ambulans 118"
  },
  "primary_survey": {
    "airway": {
      "patency": "obstructed",
      "obstruction_cause": "gurgling",
      "c_spine_control": true
    },
    "breathing": {
      "pattern": "tachypnea",
      "respiratory_rate": 32,
      "spo2": 89,
      "breath_sounds": "ronkhi_basah"
    },
    "circulation": {
      "pulse_rate": 128,
      "pulse_quality": "lemah",
      "blood_pressure": "80/50",
      "crt": "> 2 detik",
      "skin_color": "pucat_sianosis"
    },
    "disability": {
      "gcs_eye": 2,
      "gcs_verbal": 3,
      "gcs_motor": 4,
      "total_gcs": 9,
      "pupil_right": "isokor_3mm",
      "pupil_left": "isokor_3mm"
    },
    "exposure": {
      "body_temp": 35.8,
      "visible_deformities": "Fraktur tertutup femur dekstra"
    }
  },
  "secondary_survey": {
    "ample": {
      "allergies": "Tidak ada riwayat alergi",
      "medications": "Antihipertensi (Amlodipine 5mg)",
      "past_illnesses": "Hipertensi kronis",
      "last_meal": "4 jam yang lalu (nasi)",
      "events": "Kecelakaan lalu lintas motor vs mobil"
    },
    "head_to_toe": {
      "head": "Jejas di pelipis kanan",
      "neck": "Deviasi trakea negatif",
      "chest": "Asimetris saat bernapas",
      "abdomen": "Supel, bising usus normoaktif",
      "extremities": "Krepitasi pada femur dekstra"
    }
  }
}
```

---

## 3. Mekanisme Penyimpanan Draf Anti-Kehilangan Data

1. **Auto-Save Lokal (Flutter Client)**:
   * Setiap perubahan field form disimpan ke database lokal (`Drift`/`Isar`) dengan *debounce* 1000 ms.
2. **Sinkronisasi ke Server**:
   * Endpoint: `PUT /api/v1/sessions/{id}/draft`
   * Mengirim payload `assessment_payload` terkompresi.
   * Server mengembalikan timestamp draf terakhir untuk sinkronisasi multi-device.
