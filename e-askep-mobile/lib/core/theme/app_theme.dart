import 'package:flutter/material.dart';

class AppColors {
  // Brand & Accent Poltekkes Kemenkes Riau
  static const Color primary = Color(0xFF008D88);       // Hijau Toska Kemenkes
  static const Color primaryHover = Color(0xFF00736F);
  static const Color primaryTint = Color(0xFFE6F5F4);    // Light active pill
  static const Color secondary = Color(0xFFEAB308);     // Kuning Emas Poltekkes
  static const Color secondaryDeep = Color(0xFFCA8A04);
  static const Color secondaryPill = Color(0xFFFEF9C3);

  // Surface & Canvas (Clinical Clean Minimalism)
  static const Color baseCanvas = Color(0xFFF1F5F9);    // Slate-100
  static const Color cardSurface = Color(0xFFFFFFFF);   // Pure White
  static const Color sidebarSurface = Color(0xFFF8FAFC); // Slate-50
  static const Color borderLight = Color(0xFFE2E8F0);   // Slate-200

  // Typography Colors
  static const Color textHeading = Color(0xFF0F172A);   // Slate-900
  static const Color textSecondary = Color(0xFF64748B); // Slate-500
  static const Color textMuted = Color(0xFF94A3B8);     // Slate-400

  // Triase Medis KGD Semantik
  static const Color triageRed = Color(0xFFDC2626);
  static const Color triageRedBg = Color(0xFFFEF2F2);
  static const Color triageYellow = Color(0xFFD97706);
  static const Color triageYellowBg = Color(0xFFFFFBEB);
  static const Color triageGreen = Color(0xFF16A34A);
  static const Color triageGreenBg = Color(0xFFF0FDF4);
  static const Color triageBlack = Color(0xFF1E293B);
  static const Color triageBlackBg = Color(0xFFF1F5F9);
}

class AppTheme {
  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      scaffoldBackgroundColor: AppColors.baseCanvas,
      colorScheme: const ColorScheme.light(
        primary: AppColors.primary,
        secondary: AppColors.secondary,
        surface: AppColors.cardSurface,
      ),
      appBarTheme: const AppBarTheme(
        backgroundColor: AppColors.cardSurface,
        foregroundColor: AppColors.textHeading,
        elevation: 0,
        centerTitle: false,
        iconTheme: IconThemeData(color: AppColors.primary),
      ),
      cardTheme: CardThemeData(
        color: AppColors.cardSurface,
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: const BorderSide(color: AppColors.borderLight),
        ),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.primary,
          foregroundColor: Colors.white,
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
        ),
      ),
    );
  }
}
