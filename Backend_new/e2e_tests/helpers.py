"""
=======================================================
  PISE-PP — Utilitaires communs pour les tests e2e
=======================================================
"""

import sys
import json
import time
import requests
from config import (
    BASE_URL, API_PREFIX, REQUEST_TIMEOUT,
    SHOW_RESPONSE_ON_FAIL,
    GREEN, RED, YELLOW, CYAN, BOLD, RESET, DIM, MAGENTA, BLUE,
    c
)


# ══════════════════════════════════════════════════════════════
#  Compteurs globaux de résultats
# ══════════════════════════════════════════════════════════════
class TestCounter:
    def __init__(self):
        self.passed = 0
        self.failed = 0
        self.skipped = 0
        self.suite_results: list[dict] = []

    def reset(self):
        self.passed = 0
        self.failed = 0
        self.skipped = 0

    @property
    def total(self):
        return self.passed + self.failed

    def add_suite(self, name: str, passed: int, failed: int, skipped: int):
        self.suite_results.append({
            "suite": name,
            "passed": passed,
            "failed": failed,
            "skipped": skipped,
        })
        self.passed  += passed
        self.failed  += failed
        self.skipped += skipped


COUNTER = TestCounter()
_suite_counter = {"pass": 0, "fail": 0, "skip": 0}


# ══════════════════════════════════════════════════════════════
#  Fonctions d'affichage
# ══════════════════════════════════════════════════════════════

def section(title: str):
    """Affiche un titre de section."""
    print(f"\n{c('▶ ' + title, BOLD + CYAN)}")
    print(c("  " + "─" * 60, DIM))


def banner(title: str):
    """Affiche une bannière principale."""
    width = 68
    print()
    print(c("═" * width, BOLD + BLUE))
    print(c(f"  {title}", BOLD + BLUE))
    print(c("═" * width, BOLD + BLUE))


def print_result(
    method: str,
    endpoint: str,
    status: int | None,
    expected: list[int],
    elapsed: float,
    body=None,
    label: str = "",
) -> bool:
    ok = (status in expected) if status is not None else False
    icon   = c("✅ PASS", GREEN) if ok else c("❌ FAIL", RED)
    meth   = c(f"{method:<6}", CYAN)
    dur    = c(f"{elapsed*1000:6.0f}ms", DIM)
    ep     = label or endpoint
    status_str = c(str(status) if status else "ERR", GREEN if ok else RED)

    print(f"    {icon}  {meth} {ep:<52} {status_str}  {dur}")

    if not ok and SHOW_RESPONSE_ON_FAIL and body:
        try:
            parsed = body if isinstance(body, dict) else json.loads(body)
            msg = (
                parsed.get("message")
                or parsed.get("error")
                or str(parsed)[:200]
            )
        except Exception:
            msg = str(body)[:200]
        print(f"          {c('↳ ' + msg, YELLOW)}")

    if ok:
        _suite_counter["pass"] += 1
    else:
        _suite_counter["fail"] += 1

    return ok


# ══════════════════════════════════════════════════════════════
#  Requête HTTP générique
# ══════════════════════════════════════════════════════════════

def do_request(
    session: requests.Session,
    method: str,
    url: str,
    payload=None,
    files=None,
    data=None,
) -> tuple[int | None, float, dict | str | None]:
    """Exécute une requête et retourne (status, elapsed, body)."""
    t0 = time.time()
    try:
        if files:
            r = session.request(method, url, files=files, data=data, timeout=REQUEST_TIMEOUT)
        else:
            r = session.request(method, url, json=payload, timeout=REQUEST_TIMEOUT)
        elapsed = time.time() - t0
        try:
            body = r.json()
        except Exception:
            body = r.text
        return r.status_code, elapsed, body
    except requests.exceptions.ConnectionError:
        return None, 0, "connexion impossible"
    except Exception as e:
        return None, 0, str(e)


def api(path: str) -> str:
    """Construit l'URL complète d'un endpoint."""
    return BASE_URL + API_PREFIX + path


def test(
    session: requests.Session,
    method: str,
    path: str,
    expected=(200,),
    payload=None,
    label: str = "",
    files=None,
    data=None,
) -> tuple[int | None, dict | str | None]:
    """Effectue un test et affiche le résultat. Retourne (status, body)."""
    url = api(path)
    status, elapsed, body = do_request(session, method, url, payload, files, data)
    print_result(method, path, status, list(expected), elapsed, body, label)
    return status, body


def skip(count: int, reason: str = ""):
    """Incrémente le compteur de skips."""
    msg = f"    {c('⏭  SKIP', YELLOW)}  {c(reason, DIM) if reason else ''} ({count} test(s))"
    print(msg)
    _suite_counter["skip"] += count


def info(msg: str):
    """Affiche un message d'info."""
    print(f"    {c('ℹ', BLUE)}  {c(msg, DIM)}")


def warn(msg: str):
    """Affiche un avertissement."""
    print(f"    {c('⚠', YELLOW)}  {c(msg, YELLOW)}")


# ══════════════════════════════════════════════════════════════
#  Authentification
# ══════════════════════════════════════════════════════════════

def login(session: requests.Session, credentials: dict) -> str | None:
    """
    Authentifie un utilisateur et injecte le token Bearer dans la session.
    Retourne le token ou None si échec.
    """
    url = api("/auth/login")
    t0  = time.time()
    try:
        r = session.post(url, json=credentials, timeout=REQUEST_TIMEOUT)
        elapsed = time.time() - t0
        body = r.json() if r.headers.get("content-type", "").startswith("application") else {}
    except Exception as e:
        print(f"    {c('❌ Login échoué :', RED)} {e}")
        return None

    token = None
    if r.status_code == 200:
        data  = body.get("data", {})
        token = data.get("token") or data.get("access_token")

    ok = r.status_code == 200 and token
    print_result("POST", "/auth/login", r.status_code, [200], elapsed, body, "/auth/login")

    if token:
        session.headers.update({"Authorization": f"Bearer {token}"})
        info(f"Token obtenu pour {credentials['email']}")
    else:
        warn(f"Authentification échouée pour {credentials['email']}")

    return token


def logout(session: requests.Session):
    """Déconnecte l'utilisateur actif."""
    test(session, "POST", "/auth/logout", expected=(200,))
    session.headers.pop("Authorization", None)


# ══════════════════════════════════════════════════════════════
#  Suite tracking
# ══════════════════════════════════════════════════════════════

def begin_suite(name: str):
    """Réinitialise le compteur de suite et affiche la bannière."""
    global _suite_counter
    _suite_counter = {"pass": 0, "fail": 0, "skip": 0}
    banner(name)


def end_suite(name: str) -> tuple[int, int, int]:
    """Affiche le résumé de la suite et retourne (passed, failed, skipped)."""
    p = _suite_counter["pass"]
    f = _suite_counter["fail"]
    s = _suite_counter["skip"]
    status = c("OK", GREEN) if f == 0 else c("ÉCHEC", RED)
    print(f"\n    {c('Résumé :', BOLD)} {c(str(p), GREEN)} réussis · "
          f"{c(str(f), RED if f else GREEN)} échoués · "
          f"{c(str(s), YELLOW)} ignorés  ─  {status}")
    return p, f, s


def global_summary():
    """Affiche le résumé global de tous les tests."""
    width = 68
    print()
    print(c("═" * width, BOLD))
    print(c("  RÉSUMÉ GLOBAL DES TESTS E2E", BOLD))
    print(c("═" * width, BOLD))

    # Détail par suite
    for suite in COUNTER.suite_results:
        icon = c("✅", GREEN) if suite["failed"] == 0 else c("❌", RED)
        print(f"  {icon}  {suite['suite']:<40} "
              f"{c(str(suite['passed']), GREEN)}/{c(str(suite['passed'] + suite['failed']), BOLD)}")

    print(c("  " + "─" * 60, DIM))
    print(f"  {c('✅ Réussis  :', BOLD)} {c(str(COUNTER.passed), GREEN)}")
    print(f"  {c('❌ Échoués  :', BOLD)} {c(str(COUNTER.failed), RED if COUNTER.failed else GREEN)}")
    print(f"  {c('⏭  Ignorés  :', BOLD)} {c(str(COUNTER.skipped), YELLOW)}")
    print(f"  {c('   Total testés : ' + str(COUNTER.total), BOLD)}")

    if COUNTER.failed == 0:
        print(f"\n  {c('🎉  Tous les tests passent !', GREEN + BOLD)}")
    else:
        print(f"\n  {c(f'⚠   {COUNTER.failed} test(s) en échec.', YELLOW + BOLD)}")

    print(c("═" * width, BOLD))
    print()
