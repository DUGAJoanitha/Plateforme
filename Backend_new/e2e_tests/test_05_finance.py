"""
=======================================================
  SUITE 5 — Finance (Budgets & Dépenses)
=======================================================
  Scénarios testés :
    5.1  Lister les budgets
    5.2  Résumé financier
    5.3  Créer une ligne budgétaire
    5.4  Données invalides → 422
    5.5  Soumettre une dépense valide
    5.6  Soumettre une dépense qui dépasse le budget → 422
    5.7  Dépense sans description → 422
    5.8  Lister les budgets (vérifier le nouveau)
    5.9  Résumé mis à jour
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
    begin_suite("SUITE 5 — Finance (Budgets & Dépenses)")

    _own_session = session is None
    if _own_session:
        session = requests.Session()
        session.headers.update({"Accept": "application/json", "Content-Type": "application/json"})
        token = login(session, COORDINATOR)
        if not token:
            skip(9, "token non obtenu")
            p, f, s = end_suite("Suite 5 — Finance")
            COUNTER.add_suite("Finance", p, f, s)
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
            p, f, s = end_suite("Suite 5 — Finance")
            COUNTER.add_suite("Finance", p, f, s)
            return p, f, s

    info(f"Projet utilisé → ID = {project_id}")

    # ── 5.1 Lister les budgets ────────────────────────────────────────
    section("5.1  GET /projects/:id/finance/budgets — Liste des budgets")
    test(session, "GET", f"/projects/{project_id}/finance/budgets", expected=(200,))

    # ── 5.2 Résumé financier ──────────────────────────────────────────
    section("5.2  GET /projects/:id/finance/summary — Résumé financier")
    status, body = test(session, "GET", f"/projects/{project_id}/finance/summary",
                        expected=(200,))
    if status == 200 and isinstance(body, dict):
        summary = body.get("summary", {})
        info(f"Budget total = {summary.get('total_budget', 0):,.0f} | "
             f"Dépensé = {summary.get('total_spent', 0):,.0f} | "
             f"Restant = {summary.get('remaining', 0):,.0f}")

    # ── 5.3 Créer une ligne budgétaire ────────────────────────────────
    section("5.3  POST /finance/budgets — Création d'une ligne budgétaire")
    status, body = test(session, "POST", f"/projects/{project_id}/finance/budgets",
                        expected=(201,),
                        payload={
                            "category":        "Personnel",
                            "description":     "Salaires de l'équipe projet",
                            "allocated":       50000,
                            "alert_threshold": 85,
                        })
    budget_id = None
    if status == 201 and isinstance(body, dict):
        budget_id = body.get("data", {}).get("id")
        info(f"Ligne budgétaire créée → ID = {budget_id}")

    # ── 5.4 Données invalides ─────────────────────────────────────────
    section("5.4  POST /finance/budgets — Données invalides → 422")
    test(session, "POST", f"/projects/{project_id}/finance/budgets",
         expected=(422,),
         payload={"category": ""},   # category vide + allocated manquant
         label="/finance/budgets (payload invalide)")

    test(session, "POST", f"/projects/{project_id}/finance/budgets",
         expected=(422,),
         payload={
             "category":  "Test",
             "allocated": -100,      # montant négatif
         },
         label="/finance/budgets (allocated négatif)")

    if not budget_id:
        warn("Ligne budgétaire non créée — tests de dépenses ignorés")
        skip(4, "budget_id manquant")
        p, f, s = end_suite("Suite 5 — Finance")
        COUNTER.add_suite("Finance", p, f, s)
        return p, f, s

    # ── 5.5 Soumettre une dépense valide ──────────────────────────────
    section("5.5  POST /finance/budgets/:id/expenses — Dépense valide")
    status, body = test(
        session, "POST",
        f"/projects/{project_id}/finance/budgets/{budget_id}/expenses",
        expected=(201,),
        payload={
            "amount":      1500,
            "description": "Achat de fournitures de bureau — test e2e",
        }
    )
    if status == 201 and isinstance(body, dict):
        info(f"Dépense soumise → ID = {body.get('data', {}).get('id')}")

    # ── 5.6 Dépense dépassant le budget → 422 ────────────────────────
    section("5.6  POST /expenses — Dépassement de budget → 422")
    test(
        session, "POST",
        f"/projects/{project_id}/finance/budgets/{budget_id}/expenses",
        expected=(422,),
        payload={
            "amount":      999999999,   # montant beaucoup trop élevé
            "description": "Dépense excessive — doit être refusée",
        },
        label=f"/budgets/{budget_id}/expenses (dépassement budget)"
    )

    # ── 5.7 Dépense sans description → 422 ───────────────────────────
    section("5.7  POST /expenses — Sans description → 422")
    test(
        session, "POST",
        f"/projects/{project_id}/finance/budgets/{budget_id}/expenses",
        expected=(422,),
        payload={"amount": 100},   # description manquante
        label=f"/budgets/{budget_id}/expenses (description manquante)"
    )

    # ── 5.8 Vérification liste budgets ────────────────────────────────
    section("5.8  GET /finance/budgets — Vérification après création")
    status, body = test(session, "GET", f"/projects/{project_id}/finance/budgets",
                        expected=(200,))
    if status == 200 and isinstance(body, dict):
        budgets = body.get("data", [])
        info(f"Nombre de lignes budgétaires : {len(budgets)}")

    # ── 5.9 Résumé mis à jour ─────────────────────────────────────────
    section("5.9  GET /finance/summary — Résumé après dépense")
    status, body = test(session, "GET", f"/projects/{project_id}/finance/summary",
                        expected=(200,))
    if status == 200 and isinstance(body, dict):
        pct = body.get("summary", {}).get("consumption_percentage", 0)
        info(f"Taux de consommation global = {pct:.1f}%")

    p, f, s = end_suite("Suite 5 — Finance")
    COUNTER.add_suite("Finance", p, f, s)
    return p, f, s


if __name__ == "__main__":
    run()
