# e-Askep Mobile Client (Flutter 3.x)
## Poltekkes Kemenkes Riau

Aplikasi mobile Android untuk mahasiswa keperawatan dan Dosen / Clinical Instructor (CI) Poltekkes Kemenkes Riau.

---

## 1. Prasyarat & Lingkungan
* **Flutter SDK**: `>= 3.22.x` (Dart `>= 3.4.x`)
* **Android Studio / VS Code** dengan Flutter & Dart plugin
* **Android SDK**: API Level 34+ (Target Android 14)

---

## 2. Struktur Proyek
```text
lib/
├── core/
│   ├── constants/       <--- api_endpoints.dart
│   ├── network/         <--- Dio client & interceptor
│   └── theme/           <--- app_theme.dart (Shadcn Minimalist, Toska & Emas)
├── features/
│   ├── auth/            <--- Login Sanctum & session
│   ├── assessment/      <--- Wizard KGD, KDM, KMB & auto-draft
│   ├── eparaf/          <--- CI review desk & batch e-paraf
│   └── procedures/      <--- SPO checklist & logbook
└── main.dart            <--- Entry point
```

---

## 3. Langkah Menjalankan
```bash
# 1. Pindah ke folder mobile
cd C:/laragon/www/e-askep-riau/e-askep-mobile

# 2. Ambil dependensi (ketika Flutter SDK sudah terpasang)
flutter pub get

# 3. Generate model Drift / JSON
flutter packages pub run build_runner build --delete-conflicting-outputs

# 4. Jalankan aplikasi di emulator atau perangkat fisik
flutter run
```
