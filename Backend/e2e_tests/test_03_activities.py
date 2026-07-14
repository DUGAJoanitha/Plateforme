"""
=======================================================
  SUITE 3 — Activités (CRUD + états)
=======================================================
  Scénarios testés :
    3.1  Lister les activités d'un projet
    3.2  Créer une activité (valide)
    3.3  Créer avec données invalides → 422
    3.4  Mettre à jour une activité
    3.5  Consulter les dépendances
    3.6  Créer une activité dépendante
    3.7  Bloquer une activité
    3.8  Compléter une activité
    3.9  Supprimer une activité
    3.10 Accès à une activité d'un autre projet → 404
=======================================================
"""

import requests
from datetime import date, timedelta
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
    begin_suite("SUITE 3 — Activités")

    _own_session = session is None
    if _own_session:
        session = requests.Session()
        session.headers.update({"Accept": "application/json", "Content-Type": "application/json"})
        token = login(session, COORDINATOR)
        if not token:
            warn("Authentification échouée")
            skip(10, "token non obtenu")
            p, f, s = end_suite("Suite 3 — Activities")
            COUNTER.add_suite("Activités", p, f, s)
            return p, f, s

    # Résoudre project_id si non fourni
    if not project_id:
        status, body = test(session, "GET", "/projects", expected=(200,))
        if status == 200 and isinstance(body, dict):
            items = body.get("data", [])
            if items:
                project_id = items[0].get("id")
        if not project_id:
            warn("Aucun projet disponible — suite ignorée")
            skip(10, "project_id manquant")
            p, f, s = end_suite("Suite 3 — Activities")
            COUNTER.add_suite("Activités", p, f, s)
            return p, f, s

    info(f"Projet utilisé → ID = {project_id}")

    today      = date.today()
    start_date = (today + timedelta(days=1)).isoformat()
    end_date   = (today + timedelta(days=30)).isoformat()

    # ── 3.1 Lister les activités ──────────────────────────────────────
    section("3.1  GET /projects/:id/activities — Liste des activités")
    status, body = test(session, "GET", f"/projects/{project_id}/activities", expected=(200,))
    if status == 200 and isinstance(body, dict):
        summary = body.get("summary", {})
        info(f"Activités : {summary.get('total', '?')} total")

    # ── 3.2 Créer une activité ────────────────────────────────────────
    section("3.2  POST /projects/:id/activities — Création")
    status, body = test(session, "POST", f"/projects/{project_id}/activities",
                        expected=(201,),
                        payload={
                            "name":        "Activité E2E principale",
                            "description": "Créée par les tests automatisés",
                            "start_date":  start_date,
                            "end_date":    end_date,
                        })
    activity_id = None
    if status == 201 and isinstance(body, dict):
        activity_id = body.get("data", {}).get("id")
        info(f"Activité créée → ID = {activity_id}")

    # ── 3.3 Données invalides → 422 ───────────────────────────────────
    section("3.3  POST /projects/:id/activities — Données invalides → 422")
    test(session, "POST", f"/projects/{project_id}/activities",
         expected=(422,),
         payload={"name": ""},
         label="/activities (name vide)")

    test(session, "POST", f"/projects/{project_id}/activities",
         expected=(422,),
         payload={
             "name":       "Mauvaises dates",
             "start_date": end_date,
             "end_date":   start_date,
         },
         label="/activities (end_date < start_date)")

    if not activity_id:
        warn("Activité non créée — tests suivants ignorés")
        skip(6, "activity_id manquant")
        p, f, s = end_suite("Suite 3 — Activities")
        COUNTER.add_suite("Activités", p, f, s)
        return p, f, s

    # ── 3.4 Mettre à jour une activité ───────────────────────────────
    section("3.4  PUT /projects/:id/activities/:id — Mise à jour")
    test(session, "PUT", f"/projects/{project_id}/activities/{activity_id}",
         expected=(200,),
         payload={
             "progress": 25,
             "status":   "in_progress",
         })

    # ── 3.5 Dépendances ───────────────────────────────────────────────
    section("3.5  GET /activities/:id/dependencies — Dépendances")
    test(session, "GET",
         f"/projects/{project_id}/activities/{activity_id}/dependencies",
         expected=(200,))

    # ── 3.6 Créer une activité dépendante ────────────────────────────
    section("3.6  POST — Activité avec depends_on")
    start2 = (today + timedelta(days=31)).isoformat()
    end2   = (today + timedelta(days=60)).isoformat()
    status2, body2 = test(session, "POST", f"/projects/{project_id}/activities",
                          expected=(201,),
                          payload={
                              "name":       "Activité dépendante",
                              "start_date": start2,
                              "end_date":   end2,
                              "depends_on": activity_id,
                          })
    dep_id = None
    if status2 == 201 and isinstance(body2, dict):
        dep_id = body2.get("data", {}).get("id")
        info(f"Activité dépendante créée → ID = {dep_id}")

    # ── 3.7 Bloquer une activité ──────────────────────────────────────
    section("3.7  POST /activities/:id/block — Blocage")
    test(session, "POST", f"/projects/{project_id}/activities/{activity_id}/block",
         expected=(200,),
         payload={"reason": "Ressources indisponibles — test automatisé"})

    # ── 3.8 Compléter une activité ────────────────────────────────────
    section("3.8  POST /activities/:id/complete — Complétion")
    test(session, "POST", f"/projects/{project_id}/activities/{activity_id}/complete",
         expected=(200,),
         payload={"completion_note": "Complétée par les tests automatisés"})

    # ── 3.9 Supprimer l'activité dépendante ──────────────────────────
    section("3.9  DELETE /activities/:id — Suppression")
    if dep_id:
        test(session, "DELETE", f"/projects/{project_id}/activities/{dep_id}", expected=(200,))
    else:
        skip(1, "activité dépendante non créée")

    # ── 3.10 Accès à une activité inexistante → 404 ───────────────────
    section("3.10 GET /activities/999999 — Activité inexistante → 404")
    test(session, "GET",
         f"/projects/{project_id}/activities/999999/dependencies",
         expected=(404,),
         label="/activities/999999/dependencies (inexistant)")

    p, f, s = end_suite("Suite 3 — Activities")
    COUNTER.add_suite("Activités", p, f, s)
    return p, f, s


if __name__ == "__main__":
    run()
