#!/usr/bin/env python3
"""
===========================================================================
  PISE-PP — Runner principal des tests de bout en bout (E2E)
===========================================================================
  Usage :
    python run_e2e.py                  # toutes les suites
    python run_e2e.py --suite auth     # suite spécifique
    python run_e2e.py --suite projects activities finance
    python run_e2e.py --verbose        # affiche les détails des réponses
    python run_e2e.py --help           # aide

  Pré-requis :
    pip install requests
    php artisan serve  (dans un terminal séparé)

  Suites disponibles :
    auth        Suite 1 — Authentification & Sécurité
    projects    Suite 2 — Gestion des Projets
    activities  Suite 3 — Activités
    kpis        Suite 4 — KPIs
    finance     Suite 5 — Finance
    field       Suite 6 — Field Forms & Soumissions terrain
    reports     Suite 7 — Rapports
    ai          Suite 8 — Module IA
===========================================================================
"""

import sys
import os
import argparse
import requests

# Ajouter le répertoire courant au path pour les imports relatifs
sys.path.insert(0, os.path.dirname(__file__))

import config as cfg
from helpers import (
    begin_suite, banner, section, login, info, warn,
    global_summary, COUNTER, c
)

# Importer les suites de test
import test_01_auth      as suite_auth
import test_02_projects  as suite_projects
import test_03_activities as suite_activities
import test_04_kpis       as suite_kpis
import test_05_finance    as suite_finance
import test_06_field      as suite_field
import test_07_reports    as suite_reports
import test_08_ai         as suite_ai


# ═══════════════════════════════════════════════════════════════════════
#  ARGUMENT PARSING
# ═══════════════════════════════════════════════════════════════════════

SUITE_MAP = {
    "auth":       "Suite 1 — Authentification",
    "projects":   "Suite 2 — Projets",
    "activities": "Suite 3 — Activités",
    "kpis":       "Suite 4 — KPIs",
    "finance":    "Suite 5 — Finance",
    "field":      "Suite 6 — Field Forms",
    "reports":    "Suite 7 — Rapports",
    "ai":         "Suite 8 — Module IA",
}


def parse_args():
    parser = argparse.ArgumentParser(
        description="PISE-PP — Tests de bout en bout",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Exemples :
  python run_e2e.py
  python run_e2e.py --suite auth
  python run_e2e.py --suite projects activities kpis
  python run_e2e.py --verbose
        """,
    )
    parser.add_argument(
        "--suite", "-s",
        nargs="*",
        choices=list(SUITE_MAP.keys()),
        help="Suites à exécuter (toutes si omis)",
    )
    parser.add_argument(
        "--verbose", "-v",
        action="store_true",
        help="Afficher le corps des réponses en cas d'échec",
    )
    parser.add_argument(
        "--base-url",
        default=cfg.BASE_URL,
        help=f"URL du serveur (défaut : {cfg.BASE_URL})",
    )
    return parser.parse_args()


# ═══════════════════════════════════════════════════════════════════════
#  CRÉATION D'UNE SESSION PARTAGÉE
# ═══════════════════════════════════════════════════════════════════════

def create_session() -> requests.Session:
    session = requests.Session()
    session.headers.update({
        "Accept":       "application/json",
        "Content-Type": "application/json",
    })
    return session


# ═══════════════════════════════════════════════════════════════════════
#  MAIN RUNNER
# ═══════════════════════════════════════════════════════════════════════

def run_all(suites: list[str] | None = None, verbose: bool = False):
    """
    Exécute les suites demandées (ou toutes) dans l'ordre,
    en partageant la session et le project_id entre suites.
    """
    if verbose:
        cfg.SHOW_RESPONSE_ON_FAIL = True

    run_all_suites = (suites is None or len(suites) == 0)
    selected = set(suites) if suites else set(SUITE_MAP.keys())

    print()
    print(c("╔" + "═" * 66 + "╗", cfg.BOLD + cfg.BLUE))
    print(c("║    PISE-PP — TESTS DE BOUT EN BOUT (E2E)                        ║", cfg.BOLD + cfg.BLUE))
    print(c(f"║    Serveur : {cfg.BASE_URL:<54}║", cfg.BLUE))
    print(c("╚" + "═" * 66 + "╝", cfg.BOLD + cfg.BLUE))

    # Session partagée entre les suites
    session = create_session()

    # Token partagé — on s'authentifie une seule fois
    print(f"\n{c('🔐  Authentification initiale...', cfg.BOLD)}")
    token = login(session, cfg.COORDINATOR)
    if not token:
        warn("ERREUR : Impossible de s'authentifier. Vérifiez les credentials dans config.py")
        warn(f"Email    : {cfg.COORDINATOR['email']}")
        warn(f"Password : {cfg.COORDINATOR['password']}")
        sys.exit(1)

    # project_id partagé entre les suites (créé en suite 2 et réutilisé)
    project_id = None

    # ── Suite 1 : Auth ────────────────────────────────────────────────
    if "auth" in selected:
        suite_auth.run()   # gère sa propre session pour tester login/logout

    # ── Suite 2 : Projects ────────────────────────────────────────────
    if "projects" in selected:
        _, _, _, project_id = suite_projects.run(session=session)

    # Récupérer project_id si suite projects non exécutée
    if not project_id:
        import time
        url = cfg.BASE_URL + cfg.API_PREFIX + "/projects"
        try:
            r = session.get(url, timeout=10)
            if r.status_code == 200:
                items = r.json().get("data", [])
                if items:
                    project_id = items[0].get("id")
                    info(f"Project ID récupéré depuis l'API → {project_id}")
        except Exception:
            pass

    # ── Suite 3 : Activities ──────────────────────────────────────────
    if "activities" in selected:
        suite_activities.run(session=session, project_id=project_id)

    # ── Suite 4 : KPIs ────────────────────────────────────────────────
    if "kpis" in selected:
        suite_kpis.run(session=session, project_id=project_id)

    # ── Suite 5 : Finance ─────────────────────────────────────────────
    if "finance" in selected:
        suite_finance.run(session=session, project_id=project_id)

    # ── Suite 6 : Field Forms ─────────────────────────────────────────
    if "field" in selected:
        suite_field.run(session=session, project_id=project_id)

    # ── Suite 7 : Reports ─────────────────────────────────────────────
    if "reports" in selected:
        suite_reports.run(session=session, project_id=project_id)

    # ── Suite 8 : AI ──────────────────────────────────────────────────
    if "ai" in selected:
        suite_ai.run(session=session, project_id=project_id)

    # ── Résumé global ─────────────────────────────────────────────────
    global_summary()

    return COUNTER.failed == 0


# ═══════════════════════════════════════════════════════════════════════
#  POINT D'ENTRÉE
# ═══════════════════════════════════════════════════════════════════════

if __name__ == "__main__":
    args = parse_args()

    # Surcharger la base URL si fournie
    if args.base_url != cfg.BASE_URL:
        cfg.BASE_URL = args.base_url
        print(c(f"→ Base URL surchargée : {cfg.BASE_URL}", cfg.YELLOW))

    success = run_all(suites=args.suite, verbose=args.verbose)
    sys.exit(0 if success else 1)
