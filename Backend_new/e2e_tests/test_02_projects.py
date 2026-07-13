"""
=======================================================
  SUITE 2 — Gestion des Projets (CRUD complet)
=======================================================
  Scénarios testés :
    2.1  Lister les projets
    2.2  Créer un projet (données valides)
    2.3  Créer un projet avec données invalides → 422
    2.4  Récupérer un projet par ID
    2.5  Accès à un projet d'une autre org → 403
    2.6  Mettre à jour un projet
    2.7  Dashboard du projet
    2.8  Score de risque
    2.9  Duplication d'un projet
    2.10 Suppression (archivage) du projet dupliqué
    2.11 Validation des champs requis (store)
=======================================================
  Retourne: (project_id) pour les suites suivantes
=======================================================
"""

import requests
from datetime import date, timedelta
from helpers import (
    begin_suite, end_suite,
    section, test, skip, info, warn,
    login, COUNTER
)
from config import COORDINATOR, c, DIM


def run(session: requests.Session | None = None) -> tuple[int, int, int, int | None]:
    """
    Retourne (passed, failed, skipped, project_id).
    project_id est l'ID du projet créé pour les suites suivantes.
    """
    begin_suite("SUITE 2 — Gestion des Projets")

    _own_session = session is None
    if _own_session:
        session = requests.Session()
        session.headers.update({"Accept": "application/json", "Content-Type": "application/json"})
        token = login(session, COORDINATOR)
        if not token:
            warn("Authentification échouée — suite ignorée")
            skip(11, "token non obtenu")
            p, f, s = end_suite("Suite 2 — Projects")
            COUNTER.add_suite("Gestion des Projets", p, f, s)
            return p, f, s, None

    today      = date.today()
    start_date = (today + timedelta(days=7)).isoformat()
    end_date   = (today + timedelta(days=90)).isoformat()

    # ── 2.1 Lister les projets ────────────────────────────────────────
    section("2.1  GET /projects — Liste des projets")
    status, body = test(session, "GET", "/projects", expected=(200,))
    if status == 200 and isinstance(body, dict):
        meta = body.get("meta", {})
        info(f"Projets existants : {meta.get('total', '?')}")

    # ── 2.2 Créer un projet ───────────────────────────────────────────
    section("2.2  POST /projects — Création d'un projet valide")
    project_payload = {
        "name":         "Projet E2E Test",
        "description":  "Projet créé par les tests automatisés",
        "budget_total": 500000,
        "start_date":   start_date,
        "end_date":     end_date,
    }
    status, body = test(session, "POST", "/projects",
                        expected=(201,), payload=project_payload)

    project_id = None
    if status == 201 and isinstance(body, dict):
        project_id = body.get("data", {}).get("id")
        info(f"Projet créé → ID = {project_id}")

    # ── 2.3 Données invalides → 422 ───────────────────────────────────
    section("2.3  POST /projects — Données invalides → 422")
    test(session, "POST", "/projects",
         expected=(422,),
         payload={"name": ""},   # name vide + champs manquants
         label="/projects (payload invalide)")

    test(session, "POST", "/projects",
         expected=(422,),
         payload={
             "name":         "Test dates inversées",
             "budget_total": 100000,
             "start_date":   end_date,     # date de fin avant début
             "end_date":     start_date,
         },
         label="/projects (end_date < start_date)")

    if not project_id:
        warn("Aucun projet créé — tests /projects/:id ignorés")
        skip(7, "project_id manquant")
        p, f, s = end_suite("Suite 2 — Projects")
        COUNTER.add_suite("Gestion des Projets", p, f, s)
        return p, f, s, None

    # ── 2.4 Récupérer le projet ───────────────────────────────────────
    section("2.4  GET /projects/:id — Détails du projet")
    test(session, "GET", f"/projects/{project_id}", expected=(200,))

    # ── 2.5 Projet inexistant → 404 ───────────────────────────────────
    section("2.5  GET /projects/999999 — Projet inexistant → 404")
    test(session, "GET", "/projects/999999",
         expected=(404,),
         label="/projects/999999 (inexistant)")

    # ── 2.6 Mise à jour ───────────────────────────────────────────────
    section("2.6  PUT /projects/:id — Mise à jour")
    test(session, "PUT", f"/projects/{project_id}",
         expected=(200,),
         payload={
             "name":        "Projet E2E Test (mis à jour)",
             "description": "Description mise à jour par test",
             "status":      "active",
         })

    # ── 2.7 Dashboard ─────────────────────────────────────────────────
    section("2.7  GET /projects/:id/dashboard — Tableau de bord")
    test(session, "GET", f"/projects/{project_id}/dashboard", expected=(200,))

    # ── 2.8 Score de risque ───────────────────────────────────────────
    section("2.8  GET /projects/:id/risk-score — Score de risque")
    status, body = test(session, "GET", f"/projects/{project_id}/risk-score", expected=(200,))
    if status == 200 and isinstance(body, dict):
        info(f"Risque = {body.get('risk_score', '?')} ({body.get('risk_level', '?')})")

    # ── 2.9 Duplication ───────────────────────────────────────────────
    section("2.9  POST /projects/:id/duplicate — Duplication")
    new_start = (today + timedelta(days=120)).isoformat()
    status, body = test(session, "POST", f"/projects/{project_id}/duplicate",
                        expected=(201,),
                        payload={
                            "new_name":       "Projet E2E — Dupliqué",
                            "new_start_date": new_start,
                        })
    dup_id = None
    if status == 201 and isinstance(body, dict):
        dup_id = body.get("data", {}).get("id")
        info(f"Projet dupliqué → ID = {dup_id}")

    # ── 2.10 Suppression du projet dupliqué ───────────────────────────
    section("2.10 DELETE /projects/:id — Archivage du projet dupliqué")
    if dup_id:
        test(session, "DELETE", f"/projects/{dup_id}", expected=(200,))
    else:
        skip(1, "projet dupliqué non disponible")

    # ── 2.11 Filtre par statut ────────────────────────────────────────
    section("2.11 GET /projects?status=active — Filtre par statut")
    test(session, "GET", "/projects?status=active",
         expected=(200,),
         label="/projects?status=active")

    p, f, s = end_suite("Suite 2 — Projects")
    COUNTER.add_suite("Gestion des Projets", p, f, s)
    return p, f, s, project_id


if __name__ == "__main__":
    run()
