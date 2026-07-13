#!/usr/bin/env python3
"""
=============================================================
  PISE-PP — Script de test automatisé des endpoints API
=============================================================
  Lance : python test_endpoints.py
  Pré-requis :
    - pip install requests
    - php artisan serve (dans un terminal séparé)
    - Un utilisateur valide en base (voir CONFIG ci-dessous)
=============================================================
"""

import sys
import json
import time
import requests

# ─────────────────────────────────────────────────────────────
#  CONFIG  — à adapter selon votre environnement
# ─────────────────────────────────────────────────────────────
BASE_URL    = "http://localhost:8000/api"
API_PREFIX  = "/v1"

# Compte de test (doit exister en BDD)
TEST_EMAIL    = "coordinator@example.com"
TEST_PASSWORD = "password"

# Afficher le corps de réponse en cas d'échec
SHOW_RESPONSE_ON_FAIL = True
# ─────────────────────────────────────────────────────────────


# ──── Couleurs ANSI ────────────────────────────────────────
GREEN  = "\033[92m"
RED    = "\033[91m"
YELLOW = "\033[93m"
CYAN   = "\033[96m"
BOLD   = "\033[1m"
RESET  = "\033[0m"
DIM    = "\033[2m"

def c(text, color): return f"{color}{text}{RESET}"

# ──── Compteurs ─────────────────────────────────────────────
results = {"pass": 0, "fail": 0, "skip": 0}


def print_result(method: str, endpoint: str, status: int, expected: list[int], elapsed: float, body=None):
    ok = status in expected
    icon = c("✅ PASS", GREEN) if ok else c("❌ FAIL", RED)
    method_str = c(f"{method:<6}", CYAN)
    elapsed_str = c(f"{elapsed*1000:6.0f}ms", DIM)

    print(f"  {icon}  {method_str} {endpoint:<55} {c(str(status), GREEN if ok else RED)}  {elapsed_str}")

    if not ok and SHOW_RESPONSE_ON_FAIL and body:
        try:
            parsed = body if isinstance(body, dict) else json.loads(body)
            msg = parsed.get("message") or parsed.get("error") or str(parsed)[:200]
        except Exception:
            msg = str(body)[:200]
        print(f"        {c('↳ ' + msg, YELLOW)}")

    if ok:
        results["pass"] += 1
    else:
        results["fail"] += 1
    return ok


def request(session: requests.Session, method: str, url: str, payload=None):
    """Effectue une requête et retourne (status, elapsed, body)."""
    t0 = time.time()
    try:
        r = session.request(method, url, json=payload, timeout=15)
        elapsed = time.time() - t0
        try:
            body = r.json()
        except Exception:
            body = r.text
        return r.status_code, elapsed, body
    except requests.exceptions.ConnectionError:
        return None, 0, None
    except Exception as e:
        return None, 0, str(e)


def test(session, method, path, expected=(200,), payload=None, label=None):
    url = BASE_URL + API_PREFIX + path
    display = label or path
    status, elapsed, body = request(session, method, url, payload)
    if status is None:
        print(f"  {c('⚡ ERR ', RED)}  {c(method, CYAN):<6} {display:<55} {c('connexion impossible', RED)}")
        results["fail"] += 1
        return None, None
    print_result(method, display, status, list(expected), elapsed, body)
    return status, body


# ══════════════════════════════════════════════════════════════
#  MAIN
# ══════════════════════════════════════════════════════════════
def run():
    print()
    print(c("═" * 70, BOLD))
    print(c("  PISE-PP — Test automatisé des endpoints API", BOLD))
    print(c(f"  Serveur : {BASE_URL}", DIM))
    print(c("═" * 70, BOLD))

    session = requests.Session()
    session.headers.update({"Accept": "application/json", "Content-Type": "application/json"})

    # ── 1. Health check (route publique sans préfixe /v1) ─────
    print(f"\n{c('▶ HEALTH CHECK (public)', BOLD)}")
    h_url = BASE_URL + "/health"
    t0 = time.time()
    try:
        r = session.get(h_url, timeout=10)
        elapsed = time.time() - t0
        body = r.json() if r.headers.get("content-type","").startswith("application") else {}
        print_result("GET", "/health", r.status_code, [200], elapsed, body)
    except requests.exceptions.ConnectionError:
        print(f"  {c('❌ FAIL', RED)}  Impossible de joindre {h_url}")
        print(f"  {c('→ Avez-vous lancé : php artisan serve ?', YELLOW)}")
        sys.exit(1)

    # ── 2. Authentification (routes publiques) ─────────────────
    print(f"\n{c('▶ AUTH — routes publiques', BOLD)}")
    # Login
    status, body = test(session, "POST", "/auth/login",
                        expected=(200,),
                        payload={"email": TEST_EMAIL, "password": TEST_PASSWORD})

    token = None
    if status == 200 and isinstance(body, dict):
        data = body.get("data", {})
        token = data.get("token") or data.get("access_token")

    if not token:
        print(f"\n  {c('⚠  Authentification échouée — les tests protégés vont être ignorés.', YELLOW)}")
        print(f"  {c(f'   Vérifiez : email={TEST_EMAIL}  password={TEST_PASSWORD}', DIM)}")
    else:
        session.headers.update({"Authorization": f"Bearer {token}"})
        print(f"  {c('   Token obtenu ✔', DIM)}")

    # ── 3. Routes Auth protégées ───────────────────────────────
    print(f"\n{c('▶ AUTH — routes protégées', BOLD)}")
    test(session, "GET",  "/auth/me",       (200,))
    test(session, "GET",  "/auth/sessions", (200,))
    test(session, "GET",  "/auth/2fa/status", (200,))

    # ── 4. Projets ────────────────────────────────────────────
    print(f"\n{c('▶ PROJECTS', BOLD)}")
    test(session, "GET",  "/projects",  (200,))

    # Récupère un vrai project_id dynamiquement
    status, body = test(session, "GET", "/projects", (200,))
    project_id = None
    if status == 200 and isinstance(body, dict):
        items = body.get("data", [])
        if items:
            project_id = items[0].get("id")

    if project_id:
        print(f"  {c(f'   → Utilisation du projet ID={project_id}', DIM)}")
        test(session, "GET", f"/projects/{project_id}",           (200,))
        test(session, "GET", f"/projects/{project_id}/dashboard", (200,))
        test(session, "GET", f"/projects/{project_id}/risk-score",(200,))
    else:
        print(f"  {c('   ⚠  Aucun projet trouvé — tests /projects/:id ignorés', YELLOW)}")
        results["skip"] += 3

    # ── 5. Activities (sous projet) ───────────────────────────
    print(f"\n{c('▶ ACTIVITIES', BOLD)}")
    activity_id = None
    if project_id:
        status, body = test(session, "GET", f"/projects/{project_id}/activities", (200,))
        if status == 200 and isinstance(body, dict):
            items = body.get("data", [])
            if items:
                activity_id = items[0].get("id")
                test(session, "GET",
                     f"/projects/{project_id}/activities/{activity_id}/dependencies", (200,))
            else:
                print(f"  {c('   ⚠  Aucune activité — tests /activities/:id ignorés', YELLOW)}")
                results["skip"] += 1
    else:
        print(f"  {c('   ⚠  Pas de projet — ignoré', YELLOW)}")
        results["skip"] += 2

    # ── 6. KPIs ───────────────────────────────────────────────
    print(f"\n{c('▶ KPIs', BOLD)}")
    kpi_id = None
    if project_id:
        status, body = test(session, "GET", f"/projects/{project_id}/kpis", (200,))
        if status == 200 and isinstance(body, dict):
            items = body.get("data", [])
            if items:
                kpi_id = items[0].get("id")
                test(session, "GET", f"/projects/{project_id}/kpis/{kpi_id}/history",     (200,))
                test(session, "GET", f"/projects/{project_id}/kpis/{kpi_id}/performance",  (200,))
            else:
                print(f"  {c('   ⚠  Aucun KPI — tests /kpis/:id ignorés', YELLOW)}")
                results["skip"] += 2
    else:
        print(f"  {c('   ⚠  Pas de projet — ignoré', YELLOW)}")
        results["skip"] += 3

    # ── 7. Finance ────────────────────────────────────────────
    print(f"\n{c('▶ FINANCE', BOLD)}")
    if project_id:
        test(session, "GET", f"/projects/{project_id}/finance/budgets", (200,))
        test(session, "GET", f"/projects/{project_id}/finance/summary",  (200,))
    else:
        print(f"  {c('   ⚠  Pas de projet — ignoré', YELLOW)}")
        results["skip"] += 2

    # ── 8. Field Forms ────────────────────────────────────────
    print(f"\n{c('▶ FIELD FORMS', BOLD)}")
    form_id = None
    if project_id:
        status, body = test(session, "GET", f"/projects/{project_id}/field/forms", (200,))
        if status == 200 and isinstance(body, dict):
            items = body.get("data", [])
            if items:
                form_id = items[0].get("id")
                test(session, "GET", f"/projects/{project_id}/field/forms/{form_id}",            (200,))
                test(session, "GET", f"/projects/{project_id}/field/forms/{form_id}/submissions",(200,))
        test(session, "GET", f"/projects/{project_id}/field/map-data", (200,))
    else:
        print(f"  {c('   ⚠  Pas de projet — ignoré', YELLOW)}")
        results["skip"] += 4

    # ── 9. AI (sous projet) ───────────────────────────────────
    print(f"\n{c('▶ AI (sous projet)', BOLD)}")
    if project_id:
        test(session, "GET",  f"/projects/{project_id}/ai/recommendations", (200,))
        # Les endpoints POST AI retournent 200 ou 503 si pas de clé API
        test(session, "POST", f"/projects/{project_id}/ai/analyze",
             (200, 503),
             payload={"message": "Analyse générale du projet"})
        test(session, "POST", f"/projects/{project_id}/ai/predict-risks",
             (200, 503),
             payload={"horizon": 30})
        test(session, "POST", f"/projects/{project_id}/ai/budget-forecast",
             (200, 503),
             payload={"months": 3})
    else:
        print(f"  {c('   ⚠  Pas de projet — ignoré', YELLOW)}")
        results["skip"] += 4

    # ── 10. AI Global ─────────────────────────────────────────
    print(f"\n{c('▶ AI (global)', BOLD)}")
    test(session, "POST", "/ai/chat",
         (200, 503),
         payload={"message": "Bonjour, comment puis-je t'aider ?"})

    # ── 11. Reports ───────────────────────────────────────────
    print(f"\n{c('▶ REPORTS', BOLD)}")
    report_id = None
    if project_id:
        status, body = test(session, "GET", f"/projects/{project_id}/reports", (200,))
        if status == 200 and isinstance(body, dict):
            items = body.get("data", [])
            if items:
                report_id = items[0].get("id")

        # Génération d'un rapport (201 = créé)
        test(session, "POST", f"/projects/{project_id}/reports/generate",
             (200, 201),
             payload={"type": "summary", "format": "pdf", "title": "Test auto"})

    if report_id:
        test(session, "GET", f"/reports/{report_id}/download", (200,))
    else:
        print(f"  {c('   ⚠  Aucun rapport existant — download ignoré', YELLOW)}")
        results["skip"] += 1

    # ── RÉSUMÉ FINAL ─────────────────────────────────────────
    total = results["pass"] + results["fail"]
    print()
    print(c("═" * 70, BOLD))
    print(c("  RÉSUMÉ DES TESTS", BOLD))
    print(c("═" * 70, BOLD))
    print(f"  {c('✅ Réussis  :', BOLD)} {c(str(results['pass']), GREEN)}")
    print(f"  {c('❌ Échoués  :', BOLD)} {c(str(results['fail']), RED if results['fail'] else GREEN)}")
    print(f"  {c('⏭  Ignorés  :', BOLD)} {c(str(results['skip']), YELLOW)}")
    print(f"  {c('─' * 40, DIM)}")
    print(f"  {c('   Total testés : ' + str(total), BOLD)}")

    if results["fail"] == 0:
        print(f"\n  {c('🎉 Tous les tests passent !', GREEN + BOLD)}")
    else:
        print(f"\n  {c(f'⚠  {results[\"fail\"]} endpoint(s) en échec.', YELLOW + BOLD)}")

    print(c("═" * 70, BOLD))
    print()

    sys.exit(0 if results["fail"] == 0 else 1)


if __name__ == "__main__":
    run()
