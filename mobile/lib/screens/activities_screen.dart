import 'package:flutter/material.dart';
import '../core/theme/app_theme.dart';

class ActivitiesScreen extends StatelessWidget {
  const ActivitiesScreen({super.key});

  Widget _buildActivityItem(String time, String title, String subtitle) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          width: 50,
          child: Text(time, style: const TextStyle(color: AppColors.textSecondary, fontWeight: FontWeight.bold, fontFeatures: [FontFeature.tabularFigures()])),
        ),
        Column(
          children: [
            Container(width: 12, height: 12, decoration: const BoxDecoration(color: AppColors.brandTeal, shape: BoxShape.circle)),
            Container(width: 2, height: 50, color: AppColors.outline),
          ],
        ),
        const SizedBox(width: 16),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
              Text(subtitle, style: const TextStyle(color: AppColors.textSecondary, fontSize: 14)),
              const SizedBox(height: 24),
            ],
          ),
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Activités', style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: AppColors.surface,
        scrolledUnderElevation: 0,
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          _buildActivityItem('10:00', 'Réunion de lancement', 'Projet Forages Nord'),
          _buildActivityItem('14:30', 'Inspection site', 'Village de Koudougou'),
          _buildActivityItem('16:00', 'Validation budget', 'Étape 2 terminée'),
        ],
      ),
    );
  }
}
