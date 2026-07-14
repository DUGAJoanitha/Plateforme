import 'package:flutter/material.dart';
import '../core/theme/app_theme.dart';
import '../core/widgets/scale_button.dart';

class SettingsScreen extends StatelessWidget {
  const SettingsScreen({super.key});

  Widget _buildSettingsTile(IconData icon, String title, VoidCallback onTap, {bool isDestructive = false}) {
    return ScaleButton(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppColors.outline, width: 0.5),
        ),
        child: Row(
          children: [
            Icon(icon, color: isDestructive ? AppColors.error : AppColors.textSecondary),
            const SizedBox(width: 16),
            Expanded(
              child: Text(
                title,
                style: TextStyle(
                  fontWeight: FontWeight.w500,
                  color: isDestructive ? AppColors.error : AppColors.textPrimary,
                ),
              ),
            ),
            Icon(Icons.chevron_right, color: isDestructive ? AppColors.error : AppColors.textSecondary),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Paramètres', style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: AppColors.surface,
        scrolledUnderElevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            const CircleAvatar(
              radius: 40,
              backgroundColor: AppColors.brandTeal,
              child: Text('NS', style: TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.bold)),
            ),
            const SizedBox(height: 16),
            const Text('Néo Start Admin', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
            const Text('admin@neostart.org', style: TextStyle(color: AppColors.textSecondary)),
            const SizedBox(height: 32),
            _buildSettingsTile(Icons.person_outline, 'Mon profil', () {}),
            _buildSettingsTile(Icons.notifications_none, 'Notifications', () {}),
            _buildSettingsTile(Icons.security, 'Sécurité', () {}),
            _buildSettingsTile(Icons.help_outline, 'Aide et support', () {}),
            const SizedBox(height: 24),
            _buildSettingsTile(Icons.logout, 'Se déconnecter', () {}, isDestructive: true),
          ],
        ),
      ),
    );
  }
}
