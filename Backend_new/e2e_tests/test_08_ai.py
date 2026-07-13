"""
=======================================================
  SUITE 8 — Module IA (Analyse & Recommandations)
=======================================================
  Scénarios testés :
    8.1  Analyse IA d'un projet
    8.2  Prédiction des risques
    8.3  Prévision budgétaire
    8.4  Recommandations IA
    8.5  Chat IA global (sans contexte projet)
    8.6  Marquer une recommandation comme lue
    8.7  Payload manquant → 422
=======================================================
  Note : Ces endpoints retournent 200 (IA disponible)
         ou 500/503 (clé API non configurée). Les deux
         sont acceptés pour éviter des faux positifs.
=======================================================
"""

import requests
from helpers import (
    begin_suite, end_suite,
    section, test, skip, info, warn,
    login, COUNTER
)
from config import COORDINATOR


def run(
    session: requests.Session | None = None,
    project_id: int | None = None,
) -> tuple[int, int, int]:
    begin_suite("SUITE 8 — Module IA")

    _own_session = session is None
    if _own_session:
        session = requests.Session()
        session.headers.update({"Accept": "application/json", "Content-Type": "application/json"})
        token = login(session, COORDINATOR)
        if not token:
            skip(7, "token non obtenu")
            p, f, s = end_suite("Suite 8 — AI")
            COUNTER.add_suite("Module IA", p, f, s)
            return p, f, s

    # Résoudre project_id
    if not project_id:
        _, body = test(session, "GET", "/projects", expected=(200,))
        if isinstance(body, dict):
            items = body.get("data", [])
            if items:
                project_id = items[0].get("id")
        if not project_id:
            skip(7, "project_id manquant")
            p, f, s = end_suite("Suite 8 — AI")
            COUNTER.add_suite("Module IA", p, f, s)
            return p, f, s

    info(f"Projet utilisé → ID = {project_id}")

    # Les endpoints IA acceptent 200 (IA dispo) ou 500/503 (clé absente)
    ai_ok = (200, 500, 503)

    # ── 8.1 Analyse IA ────────────────────────────────────────────────
    section("8.1  POST /ai/analyze — Analyse IA du projet")
    status, body = test(session, "POST", f"/projects/{project_id}/ai/analyze",
                        expected=ai_ok,
                        payload={"message": "Analyse générale de l'avancement du projet"})
    if status == 200 and isinstance(body, dict):
        recs = body.get("recommendations", [])
        info(f"Recommandations reçues : {len(recs)}")
    elif status in (500, 503):
        info("IA non configurée (clé API absente) — résultat acceptable")

    # ── 8.2 Prédiction des risques ────────────────────────────────────
    section("8.2  POST /ai/predict-risks — Prédiction des risques")
    test(session, "POST", f"/projects/{project_id}/ai/predict-risks",
         expected=ai_ok,
         payload={"horizon": 30})

    # ── 8.3 Prévision budgétaire ──────────────────────────────────────
    section("8.3  POST /ai/budget-forecast — Prévision budgétaire")
    test(session, "POST", f"/projects/{project_id}/ai/budget-forecast",
         expected=ai_ok,
         payload={"months": 6})

    # ── 8.4 Recommandations ───────────────────────────────────────────
    section("8.4  GET /ai/recommendations — Liste des recommandations")
    status, body = test(session, "GET",
                        f"/projects/{project_id}/ai/recommendations",
                        expected=(200,))
    rec_id = None
    if status == 200 and isinstance(body, dict):
        recs = body.get("data", [])
        if recs:
            rec_id = recs[0].get("id")
            info(f"Recommandation disponible → ID = {rec_id}")
        else:
            info("Aucune recommandation IA enregistrée")

    # ── 8.5 Chat IA global ────────────────────────────────────────────
    section("8.5  POST /ai/chat — Chat IA global")
    test(session, "POST", "/ai/chat",
         expected=ai_ok,
         payload={"message": "Comment améliorer le taux d'exécution de ce projet ?"})

    # ── 8.6 Marquer une recommandation comme lue ─────────────────────
    section("8.6  DELETE /ai/recommendations/:id — Marquer comme lue")
    if rec_id:
        test(session, "DELETE", f"/ai/recommendations/{rec_id}",
             expected=(200,),
             label=f"/ai/recommendations/{rec_id} (marquer lue)")
    else:
        skip(1, "aucune recommandation disponible")

    # ── 8.7 Chat sans message → 422 ───────────────────────────────────
    section("8.7  POST /ai/chat — Sans message → 422")
    test(session, "POST", "/ai/chat",
         expected=(422,),
         payload={},
         label="/ai/chat (message manquant)")

    p, f, s = end_suite("Suite 8 — AI")
    COUNTER.add_suite("Module IA", p, f, s)
    return p, f, s


if __name__ == "__main__":
    run()
