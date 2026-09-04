# Matriks Pengujian Komprehensif Per Fitur (Testing Matrix)
## e-Askep Poltekkes Kemenkes Riau
**Sistem**: e-Askep Poltekkes Kemenkes Riau  
**Arsitektur**: Hybrid Monolith (Laravel 11) + Mobile Client (Flutter 3.x)  
**Tujuan**: Standar skenario pengujian fungsional, integrasi, keamanan data medis, dan unit test untuk setiap modul sistem.

---

## Daftar Modul Pengujian
1. [Modul 1: Otentikasi, Hak Akses & Manajemen Akun (RBAC)](#modul-1-otentikasi-hak-akses--manajemen-akun)
2. [Modul 2: Data Master Kurikulum & Kamus 3S PPNI](#modul-2-data-master-kurikulum--kamus-3s-ppni)
3. [Modul 3: Manajemen Kelompok Praktik & Penugasan Stase](#modul-3-manajemen-kelompok-praktik--penugasan-stase)
4. [Modul 4: Pengkajian Klinis Stase KGD (Triase, ABCDE, SBAR)](#modul-4-pengkajian-klinis-stase-kgd)
5. [Modul 5: Pengkajian Klinis Stase KDM (9 Henderson, 6 Benar Obat, SOAP)](#modul-5-pengkajian-klinis-stase-kdm)
6. [Modul 6: Pengkajian Klinis Stase KMB (9 Sistem Organ, Penunjang, Bedah)](#modul-6-pengkajian-klinis-stase-kmb)
7. [Modul 7: Clinical Reasoning & Formulir Perencanaan 3S](#modul-7-clinical-reasoning--formulir-perencanaan-3s)
8. [Modul 8: Logbook Tindakan SPO & Batch E-Paraf CI](#modul-8-logbook-tindakan-spo--batch-e-paraf-ci)
9. [Modul 9: Meja Review Dosen, Inline Feedback & Rubrik Sub-CPMK](#modul-9-meja-review-dosen-inline-feedback--rubrik-sub-cpmk)
10. [Modul 10: Generator PDF Resmi 1:1 Poltekkes Riau & Verifikasi QR](#modul-10-generator-pdf-resmi-11-poltekkes-riau--verifikasi-qr)

---

## Modul 1: Otentikasi, Hak Akses & Manajemen Akun

| ID Test | Skenario Pengujian | Pra-kondisi | Langkah Pengujian | Ekspektasi Hasil | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **AUTH-01** | Login Mobile Mahasiswa via Sanctum | Akun mahasiswa aktif di tabel `users` | Request `POST /api/v1/auth/login` dengan NIM & password valid | Mengembalikan bearer token Sanctum & role `mahasiswa`. Token tersimpan di EncryptedSharedPreferences. | Integration / API |
| **AUTH-02** | Login Dosen & Akses Meja Telaah | Akun dosen terdaftar di sistem | Login via Web Portal Admin/Dosen | Diarahkan ke Dashboard Dosen dengan tema Shadcn UI Toska. | Feature Test |
| **AUTH-03** | Pembatasan Akses Mahasiswa ke Menu Admin | Token mahasiswa aktif | Akses endpoint master kurikulum `/api/v1/admin/courses` | Mendapat respons `403 Forbidden` (Role Policy Guard). | Security / Unit |
| **AUTH-04** | Logout & Revokasi Token | Token bearer aktif di header | Request `POST /api/v1/auth/logout` | Token dihapus dari tabel `personal_access_tokens`; panggilan berikutnya mengembalikan `401 Unauthorized`. | API Test |

---

## Modul 2: Data Master Kurikulum & Kamus 3S PPNI

| ID Test | Skenario Pengujian | Pra-kondisi | Langkah Pengujian | Ekspektasi Hasil | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **MST-01** | Autocomplete Pencarian SDKI < 200 ms | Master SDKI terisi 149 diagnosa | Kueri API `/api/v1/master/sdki-slki-siki?search=jalan+napas` | Data diagnosa beserta tanda mayor/minor kembali dalam waktu < 200 ms via B-Tree index. | Performance / API |
| **MST-02** | Tautan Otomatis SDKI ke SLKI & SIKI | Master 3S terhubung relasional | Pilih diagnosa `D.0001` | Dropdown SLKI menampilkan `L.01001` dan SIKI menampilkan `I.01011` secara dinamis. | Feature Test |
| **MST-03** | Pemetaan Prosedur SPO ke Sub-CPMK | Kurikulum KGD aktif | Filter katalog SPO berdasarkan Sub-CPMK 2 | Menampilkan prosedur BHD/RJP, defibrilasi, dan pemasangan EKG. | Unit Test |

---

## Modul 3: Manajemen Kelompok Praktik & Penugasan Stase

| ID Test | Skenario Pengujian | Pra-kondisi | Langkah Pengujian | Ekspektasi Hasil | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **GRP-01** | Pemetaan Kelompok Bimbingan Mahasiswa | Akun mahasiswa dan dosen aktif | Admin memetakan 5 mahasiswa ke Dosen Pembimbing A di stase KGD | Record tersimpan di `student_groups`; mahasiswa melihat dosen pembimbingnya di aplikasi mobile. | Feature Test |
| **GRP-02** | Isolasi Akses Mahasiswa Bimbingan | Dosen B login | Buka antrean telaah kasus | Hanya melihat mahasiswa dalam bimbingan Dosen B, tidak melihat mahasiswa Dosen A. | Security / Policy |

---

## Modul 4: Pengkajian Klinis Stase KGD

| ID Test | Skenario Pengujian | Pra-kondisi | Langkah Pengujian | Ekspektasi Hasil | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **KGD-01** | Penentuan Kategori Triase 4 Warna | Kasus baru dibuat di mobile | Pilih kategori triase Merah (*Emergent*) | Badge triase berwarna merah `#DC2626`, latar `#FEF2F2`; data tersimpan di kolom `triage_category`. | UI / Unit |
| **KGD-02** | Pengisian Lengkap Survei Primer ABCDE | Mahasiswa di step form KGD | Input data jalan napas (gurgling), pernapasan (RR 32), sirkulasi, GCS 9 | Payload JSON tersimpan di `care_session_assessments.assessment_payload` tanpa kehilangan data. | Integration |
| **KGD-03** | Pencatatan Pemantauan TTV Berkala | Sesi kasus KGD aktif | Tambah 3 baris pemantauan TTV selang 15 menit | Data time-series tersimpan berurutan di tabel `vital_sign_monitorings`. | Feature Test |
| **KGD-04** | Formulasi Handover SBAR | Pengkajian KGD selesai | Susun SBAR (Situation, Background, Assessment, Recommendation) | Data tersimpan di `evaluations_and_handovers` dengan `format_type = 'SBAR'`. | Unit Test |

---

## Modul 5: Pengkajian Klinis Stase KDM

| ID Test | Skenario Pengujian | Pra-kondisi | Langkah Pengujian | Ekspektasi Hasil | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **KDM-01** | Pengkajian 9 Kebutuhan Dasar Henderson | Sesi stase KDM aktif | Isi data 9 domain (Oksigenasi, Nutrisi, Eliminasi, dst.) | Form wizard memvalidasi seluruh domain dan menampilkan status lengkap per tahapan. | UI / Feature |
| **KDM-02** | Verifikasi Prinsip 6 Benar Obat | Prosedur medikasi dijalankan | Centang verifikasi 6 Benar Obat pada wizard | Sistem menyimpan metadata kepatuhan farmakologis dalam payload pengkajian. | Unit Test |
| **KDM-03** | Penyusunan Catatan SOAP Harian | Mahasiswa selesai berdinas | Input lembar evaluasi SOAP | Record tersimpan di `evaluations_and_handovers` dengan `format_type = 'SOAP'`. | Feature Test |

---

## Modul 6: Pengkajian Klinis Stase KMB

| ID Test | Skenario Pengujian | Pra-kondisi | Langkah Pengujian | Ekspektasi Hasil | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **KMB-01** | Anamnesis PQRST & Genogram | Kasus baru KMB | Input PQRST nyeri dada dan riwayat penyakit keluarga | Data anamnesis terstruktur tersimpan di payload KMB. | Unit Test |
| **KMB-02** | Pemeriksaan Fisik 9 Sistem Organ | Mahasiswa di step Head-to-Toe | Input temuan inspeksi, palpasi, perkusi, auskultasi per sistem | Payload JSON memuat 9 sistem organ secara terpisah dan rapi. | Feature Test |
| **KMB-03** | Pengkajian Asuhan Perioperatif | Kasus bedah aktif | Input checklist pra-bedah dan skor pasca bedah Aldrete | Skor pemulihan terhitung otomatis dan tersimpan di database. | Unit Test |

---

## Modul 7: Clinical Reasoning & Formulir Perencanaan 3S

| ID Test | Skenario Pengujian | Pra-kondisi | Langkah Pengujian | Ekspektasi Hasil | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **3S-01** | Validasi Hubungan DS/DO dengan SDKI | Mahasiswa input data temuan | Pilih diagnosa bersihan jalan napas tidak efektif | Sistem mengingatkan jika data pendukung mayor belum terpenuhi. | Logic / Unit |
| **3S-02** | Prioritisasi Rencana Asuhan | Ada 3 diagnosa keperawatan | Tentukan urutan prioritas 1, 2, 3 | Kolom `priority_order` tersimpan rapi dan ditampilkan terurut di dokumen cetak. | Feature Test |

---

## Modul 8: Logbook Tindakan SPO & Batch E-Paraf CI

| ID Test | Skenario Pengujian | Pra-kondisi | Langkah Pengujian | Ekspektasi Hasil | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **SPO-01** | Checklist Pelaksanaan Tindakan oleh Mhs | Daftar prosedur SPO tersedia | Mahasiswa centang tindakan "Pemasangan NGT" | `is_performed = true`, `performed_at` tercatat waktu lokal. Status `is_verified` masih false. | Feature Test |
| **SPO-02** | Batch Verifikasi E-Paraf oleh Dosen | Dosen melihat 5 tindakan mhs | Klik tombol "Verifikasi E-Paraf Terpilih" | Seluruh 5 item berubah `is_verified = true`, `verified_by_id` = ID dosen, audit IP tersimpan. | Feature / API |

---

## Modul 9: Meja Review Dosen, Inline Feedback & Rubrik Sub-CPMK

| ID Test | Skenario Pengujian | Pra-kondisi | Langkah Pengujian | Ekspektasi Hasil | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **REV-01** | Pengembalian Berkas untuk Revisi | Berkas berstatus `submitted` | Dosen input catatan perbaikan dan klik "Minta Revisi" | Status berkas berubah menjadi `need_revision`; mahasiswa dapat menyunting kembali. | Workflow Test |
| **REV-02** | Penilaian Rubrik Kuantitatif & Sign-Off | Pengkajian & tindakan valid | Dosen input skor Sub-CPMK (85.5) dan bubuhkan tanda tangan digital | Data tersimpan di `session_reviews`, status berkas menjadi `approved_graded`. | Feature Test |
| **REV-03** | Immutability Berkas Approved | Status berkas `approved_graded` | Mahasiswa/API coba menyunting pengkajian | Sistem menolak dengan pesan error `403 Forbidden` / Dokumen terkunci permanen. | Security / Policy |

---

## Modul 10: Generator PDF Resmi 1:1 Poltekkes Riau & Verifikasi QR

| ID Test | Skenario Pengujian | Pra-kondisi | Langkah Pengujian | Ekspektasi Hasil | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **PDF-01** | Render Dokumen Cetak Snappy < 3 Detik | Berkas berstatus `approved_graded` | Panggil `GET /api/v1/sessions/{id}/export-pdf` | File PDF ter-generate dalam waktu < 3 detik, format margin 1:1 persis formulir Poltekkes Riau. | Performance / API |
| **PDF-02** | Validasi QR Code Keaslian Berkas | Berkas PDF tercetak | Pindai QR Code di lembar persetujuan | Browser membuka halaman validasi resmi e-Askep menampilkan status keabsahan, nama dosen penilai, dan NIP. | End-to-End |
