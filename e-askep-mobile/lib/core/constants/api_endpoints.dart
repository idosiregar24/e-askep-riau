class ApiEndpoints {
  // Base URL konfigurasi
  // Gunakan baseUrlEmulator saat menjalankan di Android Emulator
  // Gunakan baseUrlDevice jika menggunakan HP fisik via Wi-Fi/LAN Laragon
  static const String baseUrlEmulator = 'http://10.0.2.2:8000/api/v1';
  static const String baseUrlLocal = 'http://127.0.0.1:8000/api/v1';
  static const String baseUrlDevice = 'http://192.168.1.100:8000/api/v1'; // sesuaikan IP laptop Laragon

  // Default active baseUrl
  static String activeBaseUrl = baseUrlEmulator;

  // Auth
  static const String login = '/auth/login';
  static const String me = '/auth/me';
  static const String logout = '/auth/logout';
  static const String updateSignature = '/profile/signature';

  // Master Data
  static const String master3S = '/master/sdki-slki-siki';
  static const String masterSPO = '/master/spo-procedures';

  // Sesi Asuhan (Mahasiswa)
  static const String sessions = '/sessions';
  static String sessionDetail(int id) => '/sessions/$id';
  static String saveDraft(int id) => '/sessions/$id/draft';
  static String submitSession(int id) => '/sessions/$id/submit';

  // Workspace Dosen / CI
  static const String ciSubmissions = '/ci/submissions';
  static const String ciVerifyProcedures = '/ci/verify-procedures';
  static String ciRevision(int id) => '/ci/sessions/$id/revision';
  static String ciGrade(int id) => '/ci/sessions/$id/grade';

  // Export PDF
  static String exportPdf(int id) => '/sessions/$id/export-pdf';
}
