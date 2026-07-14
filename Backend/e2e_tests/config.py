"""
=======================================================
  PISE-PP — Configuration des tests de bout en bout
=======================================================
  Modifiez ce fichier pour adapter les tests à votre environnement.
"""

# ── URL du serveur backend ──────────────────────────────────────────
BASE_URL   = "http://localhost:8000/api"
API_PREFIX = "/v1"

# ── Comptes de test ─────────────────────────────────────────────────
# Compte coordinateur (rôle principal des tests)
COORDINATOR = {
    "email":    "coordinator@example.com",
    "password": "password",
}

# Compte agent terrain
FIELD_AGENT = {
    "email":    "agent@example.com",
    "password": "password",
}

# Compte manager (pour valider les dépenses)
MANAGER = {
    "email":    "manager@example.com",
    "password": "password",
}

# ── Paramètres de requête ───────────────────────────────────────────
REQUEST_TIMEOUT = 20        # secondes
SHOW_RESPONSE_ON_FAIL = True

# ── Couleurs ANSI ───────────────────────────────────────────────────
GREEN  = "\033[92m"
RED    = "\033[91m"
YELLOW = "\033[93m"
CYAN   = "\033[96m"
BOLD   = "\033[1m"
RESET  = "\033[0m"
DIM    = "\033[2m"
MAGENTA = "\033[95m"
BLUE   = "\033[94m"

def c(text, color):
    return f"{color}{text}{RESET}"
