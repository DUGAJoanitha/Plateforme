"""
=======================================================
  SUITE 6 — Field Forms & Soumissions terrain
=======================================================
  Scénarios testés :
    6.1  Lister les formulaires terrain
    6.2  Créer un formulaire (schéma JSON)
    6.3  Schéma JSON invalide → 422
    6.4  Récupérer un formulaire par ID
    6.5  Soumettre des données terrain (avec GPS)
    6.6  Soumettre des données terrain (sans GPS)
    6.7  Payload JSON invalide → 422
    6.8  Lister les soumissions d'un formulaire
    6.9  Filtrer les soumissions par statut
    6.10 Carte des données géographiques
    6.11 Synchronisation batch (mode hors-ligne)
=======================================================
"""

import requests
import json
from helpers import (
    begin_suite, end_suite,
    section, test, skip, info, warn,
    login, COUNTER, api, do_request, print_result
)
from config import COORDINATOR, FIELD_AGENT
import time


def run(
    session: requests.Session | None = None,
    project_id: int | None = None,
) -> tuple[int, int, int]:
    begin_suite("SUITE 6 — Field Forms & Soumissions terrain")

    _own_session = session is None
    if _own_session:
        session = requests.Session()
        session.headers.update({"Accept": "application/json", "Content-Type": "application/json"})
        token = login(session, COORDINATOR)
        if not token:
            skip(11, "token non obtenu")
            p, f, s = end_suite("Suite 6 — Field")
            COUNTER.add_suite("Field Forms", p, f, s)
            return p, f, s

    # Résoudre project_id
    if not project_id:
        _, body = test(session, "GET", "/projects", expected=(200,))
        if isinstance(body, dict):
            items = body.get("data", [])
            if items:
                project_id = items[0].get("id")
        if not project_id:
            skip(11, "project_id manquant")
            p, f, s = end_suite("Suite 6 — Field")
            COUNTER.add_suite("Field Forms", p, f, s)
            return p, f, s

    info(f"Projet utilisé → ID = {project_id}")

    # Schéma JSON du formulaire de collecte terrain
    schema = json.dumps({
        "type": "object",
        "properties": {
            "nb_beneficiaires":  {"type": "integer", "title": "Nombre de bénéficiaires"},
            "activite_menee":    {"type": "string",  "title": "Activité menée"},
            "problemes":         {"type": "string",  "title": "Problèmes rencontrés"},
            "recommandations":   {"type": "string",  "title": "Recommandations"},
        },
        "required": ["nb_beneficiaires", "activite_menee"],
    })

    # ── 6.1 Lister les formulaires ────────────────────────────────────
    section("6.1  GET /field/forms — Liste des formulaires")
    test(session, "GET", f"/projects/{project_id}/field/forms", expected=(200,))

    # ── 6.2 Créer un formulaire ───────────────────────────────────────
    section("6.2  POST /field/forms — Création d'un formulaire")
    status, body = test(session, "POST", f"/projects/{project_id}/field/forms",
                        expected=(201,),
                        payload={
                            "name":        "Formulaire de collecte hebdomadaire",
                            "description": "Collecte des données terrain chaque semaine",
                            "schema_json": schema,
                        })
    form_id = None
    if status == 201 and isinstance(body, dict):
        form_id = body.get("data", {}).get("id")
        info(f"Formulaire créé → ID = {form_id}")

    # ── 6.3 Schéma invalide → 422 ────────────────────────────────────
    section("6.3  POST /field/forms — Schéma JSON invalide → 422")
    test(session, "POST", f"/projects/{project_id}/field/forms",
         expected=(422,),
         payload={
             "name":        "Test invalide",
             "schema_json": "ce n'est pas du JSON valide {{{",
         },
         label="/field/forms (schema_json invalide)")

    if not form_id:
        warn("Formulaire non créé — tests suivants ignorés")
        skip(8, "form_id manquant")
        p, f, s = end_suite("Suite 6 — Field")
        COUNTER.add_suite("Field Forms", p, f, s)
        return p, f, s

    # ── 6.4 Récupérer un formulaire ───────────────────────────────────
    section("6.4  GET /field/forms/:id — Détails d'un formulaire")
    test(session, "GET", f"/projects/{project_id}/field/forms/{form_id}", expected=(200,))

    # ── 6.5 Soumettre des données avec GPS ────────────────────────────
    section("6.5  POST /field/forms/:id/submit — Soumission avec GPS")
    data_payload = json.dumps({
        "nb_beneficiaires": 127,
        "activite_menee":   "Formation sur les bonnes pratiques agricoles",
        "problemes":        "Retard dû aux pluies",
        "recommandations":  "Prévoir des tentes de protection",
    })
    status, body = test(session, "POST", f"/projects/{project_id}/field/forms/{form_id}/submit",
                        expected=(201,),
                        payload={
                            "data_json": data_payload,
                            "gps_lat":   6.1376,
                            "gps_lng":   1.2123,
                        })
    if status == 201 and isinstance(body, dict):
        sub_id = body.get("submission_id")
        info(f"Soumission créée → ID = {sub_id}")

    # ── 6.6 Soumission sans GPS ───────────────────────────────────────
    section("6.6  POST /field/forms/:id/submit — Soumission sans GPS")
    test(session, "POST", f"/projects/{project_id}/field/forms/{form_id}/submit",
         expected=(201,),
         payload={
             "data_json": json.dumps({
                 "nb_beneficiaires": 43,
                 "activite_menee":   "Distribution de semences",
             }),
         })

    # ── 6.7 Payload invalide → 422 ───────────────────────────────────
    section("6.7  POST /field/forms/:id/submit — JSON invalide → 422")
    test(session, "POST", f"/projects/{project_id}/field/forms/{form_id}/submit",
         expected=(422,),
         payload={"data_json": "INVALIDE {{"},
         label=f"/field/forms/{form_id}/submit (data_json invalide)")

    # ── 6.8 Lister les soumissions ────────────────────────────────────
    section("6.8  GET /field/forms/:id/submissions — Liste des soumissions")
    status, body = test(session, "GET",
                        f"/projects/{project_id}/field/forms/{form_id}/submissions",
                        expected=(200,))
    if status == 200 and isinstance(body, dict):
        items = body.get("data", {})
        count = items.get("total") if isinstance(items, dict) else len(items) if isinstance(items, list) else "?"
        info(f"Soumissions : {count}")

    # ── 6.9 Filtre par statut ─────────────────────────────────────────
    section("6.9  GET /submissions?status=pending — Filtre")
    test(session, "GET",
         f"/projects/{project_id}/field/forms/{form_id}/submissions?status=pending",
         expected=(200,),
         label=f"/field/forms/{form_id}/submissions?status=pending")

    # ── 6.10 Données cartographiques ──────────────────────────────────
    section("6.10 GET /field/map-data — Données GPS des soumissions")
    status, body = test(session, "GET", f"/projects/{project_id}/field/map-data",
                        expected=(200,))
    if status == 200 and isinstance(body, dict):
        points = body.get("data", [])
        info(f"Points GPS : {len(points)}")

    # ── 6.11 Sync batch (mode hors-ligne) ────────────────────────────
    section("6.11 POST /v1/field/sync-batch — Synchronisation hors-ligne")
    url = api("/field/sync-batch")
    t0  = time.time()
    payload = {
        "submissions": [
            {
                "form_id":   form_id,
                "data_json": json.dumps({
                    "nb_beneficiaires": 89,
                    "activite_menee":   "Réunion communautaire",
                }),
                "gps_lat": 6.2000,
                "gps_lng": 1.1800,
            }
        ]
    }
    st, elapsed, bd = do_request(session, "POST", url, payload=payload)
    print_result("POST", "/field/sync-batch", st, [200], elapsed, bd)
    if st == 200 and isinstance(bd, dict):
        info(f"Synced = {bd.get('synced_count', 0)} soumission(s)")

    p, f, s = end_suite("Suite 6 — Field")
    COUNTER.add_suite("Field Forms", p, f, s)
    return p, f, s


if __name__ == "__main__":
    run()
