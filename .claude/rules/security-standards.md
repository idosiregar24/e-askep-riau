# Security Standards — Perlindungan Data Medis & Integritas E-Paraf
## e-Askep Poltekkes Kemenkes Riau

---

## 1. Kerahasiaan Data Pasien & Rekam Medis Akademik

1. **Penyamaran Data Pasien (Anonymization/Pseudonymization)**:
   * Nama pasien di aplikasi pembelajaran klinis dapat disamarkan (misal: "Tn. S" atau "An. K") bila disyaratkan oleh komite etik rumah sakit jejaring.
   * Nomor Rekam Medis (RM) tidak boleh dibagikan ke pihak ketiga atau diekspos melalui API publik.
2. **Kepatuhan TLS 1.3 & HTTPS**:
   * Seluruh pertukaran data antara Mobile Flutter dan server Laravel wajib melalui HTTPS terenkripsi.
   * Sertifikat SSL/TLS wajib valid di lingkungan produksi.

---

## 2. Otentikasi Stateless & Keamanan Mobile Client

1. **Laravel Sanctum Token**:
   * Mobile app menggunakan bearer token yang diterbitkan oleh endpoint `/api/v1/auth/login`.
   * Token disimpan pada perangkat Android menggunakan keystore aman: **`EncryptedSharedPreferences`** (via package `flutter_secure_storage`).
   * Dilarang menyimpan token autentikasi di `SharedPreferences` biasa yang tidak terenkripsi.
2. **Revokasi Token saat Logout**:
   * Pemanggilan endpoint `/api/v1/auth/logout` wajib menghapus token aktif dari basis data (`$request->user()->currentAccessToken()->delete()`).

---

## 3. Otorisasi & Siklus Immutability Dokumen

1. **Validasi Transisi Status Dokumen (State Machine Guard)**:
   * Status dokumen hanya boleh berpindah sesuai alur resmi:
     * `draft` -> `submitted` (hanya oleh mahasiswa pemilik)
     * `submitted` -> `need_revision` (hanya oleh dosen pembimbing)
     * `submitted` -> `approved_graded` (hanya oleh dosen pembimbing)
     * `need_revision` -> `submitted` (hanya oleh mahasiswa pemilik)
     * `approved_graded` -> `archived` (otomatis atau oleh admin)
2. **Immutability Status `approved_graded`**:
   * Setelah berkas ditandatangani dan dinilai oleh dosen (`approved_graded`), berkas berstatus **MUTLAK TERKUNCI (READ-ONLY)**.
   * Tidak ada pihak (mahasiswa, dosen, maupun API) yang dapat mengubah data pengkajian, DS/DO, atau checklist tindakan setelah status ini aktif.

---

## 4. Keamanan Tanda Tangan Digital & Audit Trail E-Paraf

1. **Validasi Upload Aset Tanda Tangan**:
   * Unggahan gambar tanda tangan hanya boleh berformat PNG transparan dengan ukuran maksimal 1 MB.
   * File divalidasi MIME type dan disimpan di direktori privat/terproteksi:
   ```php
   $request->validate([
       'signature' => ['required', 'image', 'mimes:png', 'max:1024'],
   ]);
   ```
2. **Audit Trail Verifikasi E-Paraf**:
   * Setiap checklist tindakan SPO yang diparaf wajib mencatat:
     * `verified_by_id`: ID dosen/CI yang melakukan verifikasi.
     * `verified_at`: Waktu presisi persetujuan (timestamp server).
     * `ip_address`: Alamat IP perangkat penguji.
     * `device_info`: User-agent / model perangkat mobile.
3. **Penyematan QR Code Verifikasi pada PDF**:
   * Dokumen PDF hasil cetak menyematkan kode QR unik yang mengarah ke URL validasi keaslian dokumen di portal web e-Askep (`https://e-askep.poltekkes-riau.ac.id/verify/{uuid}`).

---

## 5. Checklist Keamanan Sebelum Rilis

```
[ ] Tidak ada hardcoded credentials atau JWT secret di source code
[ ] Seluruh endpoint mutasi (/draft, /submit, /grade) memiliki policy authorization check
[ ] Parameter input disanitasi terhadap serangan XSS dan SQL Injection
[ ] File upload tanda tangan diverifikasi ekstensi dan ukuran filenya
[ ] Dokumen final berstatus approved_graded tidak memiliki celah update
```
