# Tata Kelola & Struktur Direktori `.claude/`
## e-Askep Poltekkes Kemenkes Riau

Direktori `.claude/` berfungsi sebagai pusat kendali pengetahuan (*source of truth*), aturan arsitektur (*governance rules*), panduan fitur klinis (*clinical skills*), dokumen pengujian (*testing matrices*), dan catatan audit bagi AI Agent serta tim pengembang sistem **e-Askep Poltekkes Kemenkes Riau**.

---

## 1. Peta Direktori & Fungsi Berkas

```text
.claude/
├── CLAUDE.md                      <--- Panduan utama AI Agent & cheat-sheet developer
├── README.md                      <--- Dokumentasi tata kelola direktori ini (file ini)
├── settings.local.json            <--- Konfigurasi izin eksekusi tool Claude Code
├── audit/                         <--- Laporan audit kode & kesiapan arsitektur
│   └── AUDIT-CODEBASE.md          <--- Audit kesiapan arsitektur awal e-Askep
├── plan/                          <--- Dokumen spesifikasi produk & acuan kurikulum
│   ├── e_Askep_Poltekkes_Kemenkes_Riau_PRD_Final_Design.md  <--- PRD Final & Acuan Induk
│   ├── Instrumen_Pengkajian_Proses_Keperawatan_KDM.docx    <--- Instrumen RPS fisik KDM
│   ├── Instrumen_Pengkajian_Proses_Keperawatan_KGD.docx    <--- Instrumen RPS fisik KGD
│   └── Instrumen_Pengkajian_Proses_Keperawatan_KMB.docx    <--- Instrumen RPS fisik KMB
├── rules/                         <--- Standar mutlak penulisan kode (Architecture Rules)
│   ├── backend-standards.md       <--- Standar Laravel 11, API Controller, FormRequest, Service
│   ├── frontend-standards.md      <--- Standar UI Shadcn Minimalis, Palet Riau, Flutter UI
│   ├── database-standards.md      <--- Standar skema MySQL 8.0+ 14 tabel, JSON, indexing
│   └── security-standards.md      <--- Standar keamanan Sanctum, enkripsi mobile, audit e-paraf
├── skills/                        <--- Panduan modular implementasi alur bisnis spesifik
│   ├── _template.md               <--- Cetak biru pembuatan dokumen skill baru
│   ├── pengkajian-klinis.md       <--- Alur multi-step wizard pengkajian KGD, KDM, KMB
│   ├── integrasi-3s.md            <--- Motor penalaran klinis DS/DO ke SDKI, SLKI, SIKI
│   └── eparaf-dan-penilaian.md    <--- Alur telaah dosen, checklist SPO, e-paraf, & rubrik
└── testing/                       <--- Matriks pengujian terstruktur per fitur
    └── TESTING-PER-FITUR.md       <--- Skenario pengujian rinci 10 modul sistem
```

---

## 2. Cara Menggunakan Direktori Ini

### Bagi AI Agent (Claude / Assistant)
1. **Inisiasi Tugas**: Sebelum menulis atau menyunting kode pada modul tertentu, baca aturan terkait pada folder `rules/` dan panduan alur pada `skills/`.
2. **Kesesuaian dengan PRD**: Semua penamaan field, endpoint API, alur status berkas, dan rubrik penilaian wajib merujuk ke [`plan/e_Askep_Poltekkes_Kemenkes_Riau_PRD_Final_Design.md`](file:///C:/laragon/www/e-askep-riau/.claude/plan/e_Askep_Poltekkes_Kemenkes_Riau_PRD_Final_Design.md).
3. **Standar Desain UI**: Jangan pernah menggunakan styling generik atau warna acak. Selalu gunakan palet resmi Kemenkes/Poltekkes Riau (`#008D88`, `#EAB308`) dan komponen berbasis filosofi **Shadcn UI** sesuai [`rules/frontend-standards.md`](file:///C:/laragon/www/e-askep-riau/.claude/rules/frontend-standards.md).
4. **Verifikasi Kualitas**: Pastikan setiap perubahan diuji sesuai matriks di [`testing/TESTING-PER-FITUR.md`](file:///C:/laragon/www/e-askep-riau/.claude/testing/TESTING-PER-FITUR.md).

### Bagi Software Engineer / Tim Pengembang
* **Konsistensi Schema**: Setiap migrasi basis data baru harus mematuhi panduan normalisasi dan kolom JSON pada [`rules/database-standards.md`](file:///C:/laragon/www/e-askep-riau/.claude/rules/database-standards.md).
* **Penambahan Skill Baru**: Jika membangun modul baru yang memiliki kompleksitas tinggi (misal: integrasi SIMRS RS jejaring), salin [`skills/_template.md`](file:///C:/laragon/www/e-askep-riau/.claude/skills/_template.md) menjadi skill baru.
* **Audit Berkala**: Jalankan review berkala terhadap performa kueri MySQL, rendering PDF Snappy, dan responsivitas API dengan memperbarui dokumen pada `audit/`.
