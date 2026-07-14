import 'package:flutter/material.dart';

class AppColors {
  static const Color brandTeal = Color(0xFF0F8C95);
  static const Color brandTealVariant = Color(0xFF14B8C4);
  static const Color background = Color(0xFFE9EEF2);
  static const Color surface = Color(0xFFFFFFFF);
  static const Color textPrimary = Color(0xFF1F2937);
  static const Color textSecondary = Color(0xFF6B7280);
  static const Color outline = Color(0xFFD9E1E7);
  static const Color error = Color(0xFFBA1A1A);
  static const Color errorContainer = Color(0xFFFFDAD6);
}

class AppTheme {
  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      scaffoldBackgroundColor: AppColors.background,
      colorScheme: ColorScheme.fromSeed(
        seedColor: AppColors.brandTeal,
        primary: AppColors.brandTeal,
        surface: AppColors.surface,
        error: AppColors.error,
      ),
      fontFamily: 'Inter',
      textTheme: const TextTheme(
        displayLarge: TextStyle(color: AppColors.textPrimary, fontFeatures: [FontFeature.tabularFigures()]),
        displayMedium: TextStyle(color: AppColors.textPrimary, fontFeatures: [FontFeature.tabularFigures()]),
        displaySmall: TextStyle(color: AppColors.textPrimary, fontFeatures: [FontFeature.tabularFigures()]),
        headlineMedium: TextStyle(color: AppColors.textPrimary, fontFeatures: [FontFeature.tabularFigures()]),
        headlineSmall: TextStyle(color: AppColors.textPrimary, fontFeatures: [FontFeature.tabularFigures()]),
        titleLarge: TextStyle(color: AppColors.textPrimary, fontFeatures: [FontFeature.tabularFigures()]),
        titleMedium: TextStyle(color: AppColors.textPrimary, fontFeatures: [FontFeature.tabularFigures()]),
        titleSmall: TextStyle(color: AppColors.textPrimary, fontFeatures: [FontFeature.tabularFigures()]),
        bodyLarge: TextStyle(color: AppColors.textPrimary, fontFeatures: [FontFeature.tabularFigures()]),
        bodyMedium: TextStyle(color: AppColors.textPrimary, fontFeatures: [FontFeature.tabularFigures()]),
        bodySmall: TextStyle(color: AppColors.textSecondary, fontFeatures: [FontFeature.tabularFigures()]),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.brandTeal,
          foregroundColor: Colors.white,
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          minimumSize: const Size(40, 40), // Hit area
        ),
      ),
    );
  }
}
