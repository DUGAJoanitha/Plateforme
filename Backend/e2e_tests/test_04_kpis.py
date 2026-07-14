"""
=======================================================
  SUITE 4 — KPIs (Indicateurs de performance)
=======================================================
  Scénarios testés :
    4.1  Lister les KPIs
    4.2  Créer un KPI valide
    4.3  Création avec données invalides → 422
    4.4  Mettre à jour un KPI
    4.5  Ajouter une mesure
    4.6  Historique des mesures
    4.7  Ajouter plusieurs mesures pour calculer la tendance
    4.8  Analyse de performance
    4.9  Supprimer un KPI
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
    begin_suite("SUITE 4 — KPIs")

    _own_session = session is None
    if _own_session:
        session = requests.Session()
        session.headers.update({"Accept": "application/json", "Content-Type": "application/json"})
        token = login(session, COORDINATOR)
        if not token:
            skip(9, "token non obtenu")
            p, f, s = end_suite("Suite 4 — KPIs")
            COUNTER.add_suite("KPIs", p, f, s)
            return p, f, s

    # Résoudre project_id
    if not project_id:
        _, body = test(session, "GET", "/projects", expected=(200,))
        if isinstance(body, dict):
            items = body.get("data", [])
            if items:
                project_id = items[0].get("id")
        if not project_id:
            skip(9, "project_id manquant")
            p, f, s = end_suite("Suite 4 — KPIs")
            COUNTER.add_suite("KPIs", p, f, s)
            return p, f, s

    info(f"Projet utilisé → ID = {project_id}")

    # ── 4.1 Lister les KPIs ───────────────────────────────────────────
    section("4.1  GET /projects/:id/kpis — Liste des KPIs")
    test(session, "GET", f"/projects/{project_id}/kpis", expected=(200,))

    # ── 4.2 Créer un KPI ─────────────────────────────────────────────
    section("4.2  POST /projects/:id/kpis — Création")
    status, body = test(session, "POST", f"/projects/{project_id}/kpis",
                        expected=(201,),
                        payload={
                            "name":         "Taux de bénéficiaires atteints",
                            "description":  "Pourcentage de la population cible touchée",
                            "target_value": 80,
                            "baseline":     0,
                            "unit":         "%",
                            "frequency":    "monthly",
                        })
    kpi_id = None
    if status == 201 and isinstance(body, dict):
        kpi_id = body.get("data", {}).get("id")
        info(f"KPI créé → ID = {kpi_id}")

    # ── 4.3 Données invalides → 422 ───────────────────────────────────
    section("4.3  POST /kpis — Données invalides → 422")
    test(session, "POST", f"/projects/{project_id}/kpis",
         expected=(422,),
         payload={"name": "KPI sans unité"},   # unit manquant
         label="/kpis (unit manquant)")

    test(session, "POST", f"/projects/{project_id}/kpis",
         expected=(422,),
         payload={
             "name":         "KPI freq invalide",
             "target_value": 50,
             "unit":         "nb",
             "frequency":    "bi_annual",  # valeur non autorisée
         },
         label="/kpis (frequency invalide)")

    if not kpi_id:
        warn("KPI non créé — tests suivants ignorés")
        skip(5, "kpi_id manquant")
        p, f, s = end_suite("Suite 4 — KPIs")
        COUNTER.add_suite("KPIs", p, f, s)
        return p, f, s

    # ── 4.4 Mise à jour ───────────────────────────────────────────────
    section("4.4  PUT /kpis/:id — Mise à jour")
    test(session, "PUT", f"/projects/{project_id}/kpis/{kpi_id}",
         expected=(200,),
         payload={
             "target_value": 90,
             "frequency":    "quarterly",
         })

    # ── 4.5 Ajouter une mesure ───────────────────────────────────────
    section("4.5  POST /kpis/:id/measures — Ajout d'une mesure")
    today = date.today().isoformat()
    status, body = test(session, "POST", f"/projects/{project_id}/kpis/{kpi_id}/measures",
                        expected=(201,),
                        payload={
                            "value":        45,
                            "collected_at": today,
                            "notes":        "Première mesure de test",
                        })
    if status == 201 and isinstance(body, dict):
        perf = body.get("data", {}).get("kpi_performance")
        info(f"Performance KPI = {perf}%")

    # ── 4.6 Historique ───────────────────────────────────────────────
    section("4.6  GET /kpis/:id/history — Historique des mesures")
    test(session, "GET", f"/projects/{project_id}/kpis/{kpi_id}/history",
         expected=(200,))

    # ── 4.7 Plusieurs mesures pour la tendance ────────────────────────
    section("4.7  Ajout de mesures supplémentaires (calcul de tendance)")
    for i, (val, days_ago) in enumerate([
        (50, 30), (55, 25), (60, 20), (65, 15), (70, 10)
    ]):
        measure_date = (date.today() - timedelta(days=days_ago)).isoformat()
        test(session, "POST", f"/projects/{project_id}/kpis/{kpi_id}/measures",
             expected=(201,),
             payload={"value": val, "collected_at": measure_date},
             label=f"/kpis/{kpi_id}/measures (mesure {i+2}: {val}%)")

    # ── 4.8 Analyse de performance ────────────────────────────────────
    section("4.8  GET /kpis/:id/performance — Analyse de performance")
    status, body = test(session, "GET", f"/projects/{project_id}/kpis/{kpi_id}/performance",
                        expected=(200,))
    if status == 200 and isinstance(body, dict):
        analysis = body.get("analysis", {})
        info(f"Statut = {analysis.get('status', '?')} | "
             f"Tendance = {analysis.get('trend', '?')} | "
             f"Performance = {analysis.get('performance_percentage', '?')}%")

    # ── 4.9 Supprimer le KPI ─────────────────────────────────────────
    section("4.9  DELETE /kpis/:id — Suppression")
    test(session, "DELETE", f"/projects/{project_id}/kpis/{kpi_id}", expected=(200,))

    p, f, s = end_suite("Suite 4 — KPIs")
    COUNTER.add_suite("KPIs", p, f, s)
    return p, f, s


if __name__ == "__main__":
    run()
