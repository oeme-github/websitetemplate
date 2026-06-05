#!/bin/bash
# update.sh — Zieht den aktuellen Stand vom Repository und aktualisiert Abhängigkeiten.
# Ausführen auf dem Server: cd /var/www/<domain> && bash setup/update.sh

set -euo pipefail

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(dirname "$SCRIPT_DIR")"

info()    { echo -e "${GREEN}[✓]${NC} $*"; }
warning() { echo -e "${YELLOW}[!]${NC} $*"; }
error()   { echo -e "${RED}[✗]${NC} $*" >&2; exit 1; }

# ── Prüfungen ────────────────────────────────────────────────────────────────

[[ -f "$REPO_ROOT/composer.json" ]] || error "Kein composer.json gefunden. Skript aus dem Repo-Root ausführen."
[[ -f "$REPO_ROOT/.env" ]]          || warning ".env fehlt — bitte vor dem Neustart anlegen (Vorlage: .env.example)"

# ── Update ───────────────────────────────────────────────────────────────────

cd "$REPO_ROOT"

echo ""
echo "Repository aktualisieren..."
git pull origin main
info "git pull abgeschlossen."

echo ""
echo "PHP-Abhängigkeiten aktualisieren..."
composer install --no-dev --optimize-autoloader --quiet \
    --ignore-platform-req=ext-dom \
    --ignore-platform-req=ext-json \
    --ignore-platform-req=ext-libxml \
    --ignore-platform-req=ext-phar \
    --ignore-platform-req=ext-tokenizer \
    --ignore-platform-req=ext-xml \
    --ignore-platform-req=ext-xmlwriter
info "composer install abgeschlossen."

# ── Fertig ───────────────────────────────────────────────────────────────────

echo ""
info "Update abgeschlossen."
echo ""
warning "Apache neu laden, damit Änderungen aktiv werden:"
echo "    sudo systemctl reload apache2"
echo ""
