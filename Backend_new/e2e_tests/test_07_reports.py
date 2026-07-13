"""
=======================================================
  SUITE 7 — Rapports (Génération & Téléchargement)
=======================================================
  Scénarios testés :
    7.1  Lister les rapports existants
    7.2  Générer un rapport de type "summary" (PDF)
    7.3  Générer un rapport de type "activities" (PDF)
    7.4  Générer un rapport de type "financial" (PDF)
    7.5  Générer un rapport de type "full" (PDF)
    7.6  Générer un rapport Excel
    7.7  Type de rapport invalide → 422
    7.8  Télécharger le dernier rapport généré
    7.9  Supprimer un rapport
    7.10 Télécharger un rapport supprimé → 404
=======================================================
"""

import requests
import time
from helpers import (
    begin_suite, end_suite,
    section, test, skip, info, warn,
    login, api, do_request, print_result, COUNTER
)
from config import COORDINATOR


def run(
    session: requests.Session | None = None,
    project_id: int | None = None,
) -> tuple[int, int, int]:
    begin_suite("SUITE 7 — Rapports")

    _own_session = session is None
    if _own_session:
        session = requests.Session()
        session.headers.update({"Accept": "application/json", "Content-Type": "application/json"})
        token = login(session, COORDINATOR)
        if not token:
            skip(10, "token non obtenu")
            p, f, s = end_suite("Suite 7 — Reports")
            COUNTER.add_suite("Rapports", p, f, s)
            return p, f, s

    # Résoudre project_id
    if not project_id:
        _, body = test(session, "GET", "/projects", expected=(200,))
        if isinstance(body, dict):
            items = body.get("data", [])
            if items:
                project_id = items[0].get("id")
        if not project_id:
            skip(10, "project_id manquant")
            p, f, s = end_suite("Suite 7 — Reports")
            COUNTER.add_suite("Rapports", p, f, s)
            return p, f, s

    info(f"Projet utilisé → ID = {project_id}")

    # ── 7.1 Lister les rapports ───────────────────────────────────────
    section("7.1  GET /projects/:id/reports — Liste des rapports")
    test(session, "GET", f"/projects/{project_id}/reports", expected=(200,))

    # ── 7.2 Rapport "summary" PDF ─────────────────────────────────────
    section("7.2  POST /reports/generate — type=summary (PDF)")
    status, body = test(session, "POST", f"/projects/{project_id}/reports/generate",
                        expected=(200, 201),
                        payload={
                            "type":   "summary",
                            "format": "pdf",
                            "title":  "Rapport Synthèse — Test E2E",
                        })
    last_report_id = None
    if status in (200, 201) and isinstance(body, dict):
        last_report_id = body.get("data", {}).get("id")
        info(f"Rapport créé → ID = {last_report_id}")

    # ── 7.3 Rapport "activities" PDF ──────────────────────────────────
    section("7.3  POST /reports/generate — type=activities (PDF)")
    status, body = test(session, "POST", f"/projects/{project_id}/reports/generate",
                        expected=(200, 201),
                        payload={
                            "type":   "activities",
                            "format": "pdf",
                            "title":  "Rapport Activités — Test E2E",
                        })
    if status in (200, 201) and isinstance(body, dict):
        rid = body.get("data", {}).get("id")
        info(f"Rapport activités → ID = {rid}")

    # ── 7.4 Rapport "financial" PDF ───────────────────────────────────
    section("7.4  POST /reports/generate — type=financial (PDF)")
    test(session, "POST", f"/projects/{project_id}/reports/generate",
         expected=(200, 201),
         payload={
             "type":   "financial",
             "format": "pdf",
             "title":  "Rapport Financier — Test E2E",
         })

    # ── 7.5 Rapport "full" PDF ────────────────────────────────────────
    section("7.5  POST /reports/generate — type=full (PDF)")
    test(session, "POST", f"/projects/{project_id}/reports/generate",
         expected=(200, 201),
         payload={
             "type":   "full",
             "format": "pdf",
             "title":  "Rapport Complet — Test E2E",
         })

    # ── 7.6 Rapport Excel ─────────────────────────────────────────────
    section("7.6  POST /reports/generate — format=excel")
    status, body = test(session, "POST", f"/projects/{project_id}/reports/generate",
                        expected=(200, 201),
                        payload={
                            "type":   "summary",
                            "format": "excel",
                            "title":  "Export Excel — Test E2E",
                        })
    excel_report_id = None
    if status in (200, 201) and isinstance(body, dict):
        excel_report_id = body.get("data", {}).get("id")
        info(f"Rapport Excel → ID = {excel_report_id}")

    # ── 7.7 Type invalide → 422 ───────────────────────────────────────
    section("7.7  POST /reports/generate — type invalide → 422")
    test(session, "POST", f"/projects/{project_id}/reports/generate",
         expected=(422,),
         payload={
             "type":   "invalide_type",
             "format": "pdf",
         },
         label="/reports/generate (type invalide)")

    # ── 7.8 Télécharger le rapport ────────────────────────────────────
    section("7.8  GET /reports/:id/download — Téléchargement")
    if last_report_id:
        url     = api(f"/reports/{last_report_id}/download")
        t0      = time.time()
        r       = session.get(url, timeout=30)
        elapsed = time.time() - t0
        ok = print_result("GET", f"/reports/{last_report_id}/download",
                          r.status_code, [200], elapsed,
                          {"size": len(r.content)})
        if ok:
            ct = r.headers.get("Content-Type", "")
            info(f"Type de contenu : {ct} | Taille : {len(r.content):,} octets")
    else:
        skip(1, "last_report_id manquant")

    # ── 7.9 Supprimer un rapport ──────────────────────────────────────
    section("7.9  DELETE /reports/:id — Suppression")
    to_delete = excel_report_id or last_report_id
    if to_delete:
        test(session, "DELETE", f"/reports/{to_delete}", expected=(200,))
    else:
        skip(1, "rapport à supprimer non disponible")

    # ── 7.10 Télécharger après suppression → 404 ──────────────────────
    section("7.10 GET /reports/:id/download — Après suppression → 404")
    if to_delete:
        test(session, "GET", f"/reports/{to_delete}/download",
             expected=(404,),
             label=f"/reports/{to_delete}/download (supprimé)")
    else:
        skip(1, "rapport supprimé non disponible")

    p, f, s = end_suite("Suite 7 — Reports")
    COUNTER.add_suite("Rapports", p, f, s)
    return p, f, s


if __name__ == "__main__":
    run()
