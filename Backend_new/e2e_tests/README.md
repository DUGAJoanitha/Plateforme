# PISE-PP — Tests de bout en bout (E2E)

Ce dossier contient les scripts Python de tests de bout en bout pour l'API PISE-PP.

## Structure

```
e2e_tests/
├── config.py           # Configuration (URLs, credentials, couleurs)
├── helpers.py          # Utilitaires communs (requêtes, affichage, compteurs)
├── run_e2e.py          # ★ Runner principal — à lancer pour tout tester
├── test_01_auth.py     # Suite 1 — Authentification & Sécurité
├── test_02_projects.py # Suite 2 — Gestion des Projets (CRUD complet)
├── test_03_activities.py # Suite 3 — Activités (CRUD + états)
├── test_04_kpis.py     # Suite 4 — KPIs (mesures, performance, tendance)
├── test_05_finance.py  # Suite 5 — Finance (budgets & dépenses)
├── test_06_field.py    # Suite 6 — Field Forms & Soumissions terrain
├── test_07_reports.py  # Suite 7 — Rapports (PDF & Excel)
└── test_08_ai.py       # Suite 8 — Module IA
```

## Pré-requis

```bash
pip install requests
```

## Démarrage rapide

```bash
# 1. Lancer le serveur backend
php artisan serve

# 2. Lancer tous les tests
cd e2e_tests
python run_e2e.py

# Lancer une seule suite
python run_e2e.py --suite auth
python run_e2e.py --suite projects activities

# Afficher les réponses en cas d'échec
python run_e2e.py --verbose

# Aide
python run_e2e.py --help
```

## Suites de tests

| Suite | Fichier | Scénarios |
|-------|---------|-----------|
| `auth` | test_01_auth.py | Login, logout, 2FA, sessions, sécurité |
| `projects` | test_02_projects.py | CRUD, dashboard, risque, duplication |
| `activities` | test_03_activities.py | CRUD, blocage, complétion, dépendances |
| `kpis` | test_04_kpis.py | CRUD, mesures, historique, performance |
| `finance` | test_05_finance.py | Budgets, dépenses, dépassement, résumé |
| `field` | test_06_field.py | Formulaires, soumissions GPS, sync offline |
| `reports` | test_07_reports.py | Génération PDF/Excel, téléchargement |
| `ai` | test_08_ai.py | Analyse, risques, budget forecast, chat |

## Configuration

Éditez `config.py` pour modifier :
- `BASE_URL` — URL du serveur backend
- `COORDINATOR` — Compte de test principal
- `FIELD_AGENT` — Compte agent terrain
- `MANAGER` — Compte manager

## Codes de sortie

- `0` → Tous les tests passent
- `1` → Au moins un test en échec
