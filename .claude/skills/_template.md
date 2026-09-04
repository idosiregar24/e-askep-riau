# Skill: Implementasi Modul {NamaModul}
## e-Askep Poltekkes Kemenkes Riau

> **Kapan Digunakan**: Dibaca saat mengimplementasikan atau mengembangkan fitur `{NamaModul}` pada backend Laravel maupun mobile Flutter.

---

## 1. Ringkasan Fitur & Peran Pengguna
* **Target Modul**: {Sebutkan modul dan stase terkait, misal: Pengkajian KGD / SPO KDM}
* **Peran Terkait**: Mahasiswa / Dosen CI / Admin
* **Tujuan Klinis**: {Jelaskan tujuan kompetensi kurikulum RPS Poltekkes Riau}

---

## 2. Struktur Data & Entitas Terkait

```text
Tabel Utama: {nama_tabel}
Relasi:
- {nama_relasi_1} (BelongsTo/HasMany)
- {nama_relasi_2} (BelongsTo/HasMany)
Format JSON Payload:
{
  "section_name": { ... }
}
```

---

## 3. Alur Logika Bisnis & Endpoint API

| Method | Endpoint | Deskripsi | Otorisasi |
|---|---|---|---|
| `GET` | `/api/v1/{resource}` | Mengambil data | Role terdaftar |
| `POST` | `/api/v1/{resource}` | Membuat rekaman baru | Policy check |

---

## 4. Standar UI Web & Mobile
* **Web**: Ikuti standar Floating Card Shadcn UI dengan palet Toska Kemenkes (`#008D88`).
* **Mobile**: Komponen BLoC dengan validasi form lokal dan mekanisme auto-draft.

---

## 5. Checklist Verifikasi Fitur
```
[ ] Validasi FormRequest mencakup semua field mandatory
[ ] Unit test / Feature test memverifikasi alur positif dan negatif
[ ] Data tersimpan presisi di database (termasuk kolom JSON)
[ ] Tampilan responsif dan mematuhi palet warna Poltekkes Riau
```
