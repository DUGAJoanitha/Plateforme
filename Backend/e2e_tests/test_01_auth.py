"""
=======================================================
  SUITE 1 — Authentification & Sécurité
=======================================================
  Scénarios testés :
    1.1  Health check (endpoint public)
    1.2  Login valide → obtention du token
    1.3  Login invalide → rejet 401/422
    1.4  Accès sans token → 401
    1.5  /auth/me → profil utilisateur
    1.6  /auth/sessions → liste des sessions
    1.7  Statut 2FA
    1.8  Rafraîchissement de token
    1.9  Déconnexion (logout)
    1.10 Accès après logout → 401
=======================================================
"""

import requests
from helpers import (
    begin_suite, end_suite,
    section, test, skip, info, warn,
    login, logout, api, do_request, print_result,
    COUNTER
)
from config import COORDINATOR, c, GREEN, RED, YELLOW, BOLD, DIM


def run() -> tuple[int, int, int]:
    begin_suite("SUITE 1 — Authentification & Sécurité")

    session = requests.Session()
    session.headers.update({"Accept": "application/json", "Content-Type": "application/json"})

    # ── 1.1 Health check ──────────────────────────────────────────────
    section("1.1  Health check (route publique)")
    import time
    url = api("").replace("/v1", "") + "/health"
    t0 = time.time()
    try:
        r = session.get(url, timeout=10)
        elapsed = time.time() - t0
        body = r.json() if "application" in r.headers.get("content-type", "") else {}
        ok = print_result("GET", "/health", r.status_code, [200], elapsed, body)
        if not ok:
            warn("Le serveur ne répond pas. Avez-vous lancé 'php artisan serve' ?")
            p, f, s = end_suite("Suite 1 — Auth")
            COUNTER.add_suite("Authentification & Sécurité", p, f, s)
            return p, f, s
    except Exception as e:
        warn(f"Impossible de joindre le serveur : {e}")
        p, f, s = end_suite("Suite 1 — Auth")
        COUNTER.add_suite("Authentification & Sécurité", p, f, s)
        return p, f, s

    # ── 1.2 Login valide ─────────────────────────────────────────────
    section("1.2  Login valide")
    token = login(session, COORDINATOR)

    if not token:
        warn("Authentification échouée — reste de la suite ignoré")
        skip(8, "token non obtenu")
        p, f, s = end_suite("Suite 1 — Auth")
        COUNTER.add_suite("Authentification & Sécurité", p, f, s)
        return p, f, s

    # ── 1.3 Login invalide ───────────────────────────────────────────
    section("1.3  Login avec mauvais mot de passe → 401")
    bad_session = requests.Session()
    bad_session.headers.update({"Accept": "application/json", "Content-Type": "application/json"})
    test(bad_session, "POST", "/auth/login",
         expected=(401, 422),
         payload={"email": COORDINATOR["email"], "password": "MAUVAIS_MOT_DE_PASSE"},
         label="/auth/login (mauvais password)")

    # ── 1.4 Accès sans token ─────────────────────────────────────────
    section("1.4  Accès route protégée sans token → 401")
    no_auth = requests.Session()
    no_auth.headers.update({"Accept": "application/json"})
    test(no_auth, "GET", "/auth/me",
         expected=(401,),
         label="/auth/me (sans token)")
    test(no_auth, "GET", "/projects",
         expected=(401,),
         label="/projects (sans token)")

    # ── 1.5 Profil utilisateur ───────────────────────────────────────
    section("1.5  GET /auth/me — Profil utilisateur")
    status, body = test(session, "GET", "/auth/me", expected=(200,))
    if status == 200 and isinstance(body, dict):
        user = body.get("data", body)
        info(f"Utilisateur : {user.get('name', '?')} — rôle : {user.get('role', '?')}")

    # ── 1.6 Sessions ─────────────────────────────────────────────────
    section("1.6  GET /auth/sessions — Liste des sessions actives")
    test(session, "GET", "/auth/sessions", expected=(200,))

    # ── 1.7 Statut 2FA ───────────────────────────────────────────────
    section("1.7  GET /auth/2fa/status — État de la 2FA")
    test(session, "GET", "/auth/2fa/status", expected=(200,))

    # ── 1.8 Refresh token ────────────────────────────────────────────
    section("1.8  POST /auth/refresh — Renouvellement du token")
    test(session, "POST", "/auth/refresh", expected=(200,))

    # ── 1.9 Logout ───────────────────────────────────────────────────
    section("1.9  POST /auth/logout — Déconnexion")
    logout(session)

    # ── 1.10 Accès après logout ──────────────────────────────────────
    section("1.10 Accès après logout → 401")
    test(session, "GET", "/auth/me",
         expected=(401,),
         label="/auth/me (après logout)")

    p, f, s = end_suite("Suite 1 — Auth")
    COUNTER.add_suite("Authentification & Sécurité", p, f, s)
    return p, f, s


if __name__ == "__main__":
    run()
