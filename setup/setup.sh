#!/bin/bash
# setup.sh — Richtet eine neue Instanz des Website-Templates auf Ubuntu/Debian ein.
#
# Was dieses Skript tut:
#   1. Systempakete installieren (Apache, PHP, Composer, Git, Certbot)
#   2. SSH-Deploy-Key für GitHub prüfen / erzeugen
#   3. Repository nach /var/www/<domain>/ klonen
#   4. Apache-vHost konfigurieren und aktivieren
#   5. SSL via Let's Encrypt einrichten
#   6. .env interaktiv anlegen
#   7. composer install --no-dev ausführen
#   8. Dateirechte setzen
#
# Voraussetzungen:
#   - Ubuntu 22.04+ / Debian 12+
#   - sudo-Rechte
#   - Domain zeigt bereits per A-Record auf diesen Server (für SSL)

set -euo pipefail

# ── Farben ───────────────────────────────────────────────────────────────────

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
BOLD='\033[1m'
NC='\033[0m'

info()    { echo -e "${GREEN}[✓]${NC} $*"; }
step()    { echo -e "\n${BLUE}${BOLD}── $* ${NC}"; }
warning() { echo -e "${YELLOW}[!]${NC} $*"; }
error()   { echo -e "${RED}[✗]${NC} $*" >&2; exit 1; }
ask()     { echo -e "${YELLOW}[?]${NC} $*"; }

# ── Root-Check ───────────────────────────────────────────────────────────────

[[ $EUID -eq 0 ]] || error "Bitte mit sudo ausführen: sudo bash setup/setup.sh"
REAL_USER=${SUDO_USER:-$USER}
REAL_HOME=$(eval echo "~$REAL_USER")

# ── Banner ───────────────────────────────────────────────────────────────────

echo ""
echo -e "${BOLD}╔════════════════════════════════════╗${NC}"
echo -e "${BOLD}║   Website Template – Setup         ║${NC}"
echo -e "${BOLD}╚════════════════════════════════════╝${NC}"
echo ""

# ── Eingaben ─────────────────────────────────────────────────────────────────

step "Konfiguration"

read -rp "Domain (z.B. example.com): " DOMAIN
[[ -n "$DOMAIN" ]] || error "Domain darf nicht leer sein."

read -rp "Git-Repository (SSH-URL, z.B. git@github.com:user/repo.git): " REPO_URL
[[ -n "$REPO_URL" ]] || error "Repository-URL darf nicht leer sein."

read -rp "E-Mail für Let's Encrypt: " LE_EMAIL
[[ -n "$LE_EMAIL" ]] || error "E-Mail darf nicht leer sein."

REPO_PATH="/var/www/$DOMAIN"
CONF_FILE="/etc/apache2/sites-available/$DOMAIN.conf"

echo ""
echo -e "  Domain:      ${BOLD}$DOMAIN${NC}"
echo -e "  Repo-Pfad:   ${BOLD}$REPO_PATH${NC}"
echo -e "  Repository:  ${BOLD}$REPO_URL${NC}"
echo -e "  SSL-E-Mail:  ${BOLD}$LE_EMAIL${NC}"
echo ""
read -rp "Fortfahren? [j/N] " CONFIRM
[[ "$CONFIRM" =~ ^[jJyY]$ ]] || { echo "Abgebrochen."; exit 0; }

# ── Pakete installieren ───────────────────────────────────────────────────────

step "1/7 – Systempakete installieren"

apt-get update -qq
apt-get install -y -qq \
    apache2 \
    php \
    php-cli \
    php-curl \
    php-mbstring \
    php-xml \
    php-zip \
    php-intl \
    libapache2-mod-php \
    composer \
    git \
    certbot \
    python3-certbot-apache \
    dnsutils \
    curl

a2enmod rewrite ssl headers > /dev/null 2>&1

# PHP-Modul für Apache aktivieren (Version aus installierten mods ableiten)
PHP_MOD=$(ls /etc/apache2/mods-available/php*.load 2>/dev/null | head -1 | xargs -I{} basename {} .load)
if [[ -n "$PHP_MOD" ]]; then
    a2enmod "$PHP_MOD" > /dev/null 2>&1
    info "Apache-Modul $PHP_MOD aktiviert."
else
    warning "Kein PHP-Apache-Modul gefunden — ggf. manuell aktivieren: sudo a2enmod php<version>"
fi

info "Pakete und Apache-Module bereit."

# ── SSH-Deploy-Key ────────────────────────────────────────────────────────────

step "2/7 – SSH-Deploy-Key für GitHub"

SSH_KEY="$REAL_HOME/.ssh/id_ed25519"

if [[ ! -f "$SSH_KEY" ]]; then
    echo "Kein SSH-Key gefunden — wird erzeugt..."
    sudo -u "$REAL_USER" ssh-keygen -t ed25519 -C "deploy@$DOMAIN" -f "$SSH_KEY" -N ""
    info "SSH-Key erzeugt: $SSH_KEY"
fi

echo ""
echo -e "${YELLOW}Öffentlicher Key — bitte als Deploy Key im GitHub-Repo hinterlegen:${NC}"
echo -e "${BOLD}Repository → Settings → Deploy Keys → Add deploy key${NC}"
echo ""
cat "${SSH_KEY}.pub"
echo ""
ask "Deploy Key in GitHub hinterlegt und gespeichert? [j/N]"
read -rp "" CONFIRM
[[ "$CONFIRM" =~ ^[jJyY]$ ]] || { warning "Bitte Deploy Key hinterlegen und setup.sh erneut ausführen."; exit 0; }

echo "Verbindung zu GitHub testen..."
if sudo -u "$REAL_USER" ssh -T git@github.com -o StrictHostKeyChecking=accept-new 2>&1 | grep -q "successfully authenticated"; then
    info "GitHub-Verbindung erfolgreich."
else
    warning "SSH-Test lieferte kein 'successfully authenticated' — Verbindung prüfen."
    ask "Trotzdem fortfahren? [j/N]"
    read -rp "" CONFIRM
    [[ "$CONFIRM" =~ ^[jJyY]$ ]] || exit 1
fi

# ── Repository klonen ─────────────────────────────────────────────────────────

step "3/7 – Repository klonen"

if [[ -d "$REPO_PATH/.git" ]]; then
    info "Repository bereits vorhanden unter $REPO_PATH — überspringe Clone."
else
    [[ -d "$REPO_PATH" ]] && error "$REPO_PATH existiert bereits (kein Git-Repo). Bitte manuell prüfen."
    sudo -u "$REAL_USER" git clone "$REPO_URL" "$REPO_PATH"
    info "Repository geklont nach $REPO_PATH"
fi

# ── Apache-vHost ──────────────────────────────────────────────────────────────

step "4/7 – Apache-vHost konfigurieren"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TPL_FILE="$SCRIPT_DIR/apache/vhost.conf.tpl"

[[ -f "$TPL_FILE" ]] || error "vhost.conf.tpl nicht gefunden unter $TPL_FILE"

sed \
    -e "s|__DOMAIN__|$DOMAIN|g" \
    -e "s|__REPO_PATH__|$REPO_PATH|g" \
    "$TPL_FILE" > "$CONF_FILE"

a2ensite "$DOMAIN.conf" > /dev/null 2>&1
systemctl reload apache2
info "vHost $DOMAIN aktiviert."

# ── SSL / Let's Encrypt ───────────────────────────────────────────────────────

step "5/7 – SSL einrichten"

SERVER_IP=$(curl -s --max-time 5 https://api.ipify.org 2>/dev/null || echo "unbekannt")
DOMAIN_IP=$(dig +short "$DOMAIN" 2>/dev/null | grep -E '^[0-9]+\.' | head -1 || echo "")

if [[ "$SERVER_IP" != "$DOMAIN_IP" ]]; then
    warning "DNS-Check: $DOMAIN zeigt auf ${DOMAIN_IP:-'(nicht auflösbar)'}, dieser Server ist $SERVER_IP"
    warning "Certbot schlägt fehl wenn die Domain nicht auf diesen Server zeigt."
    ask "Trotzdem SSL einrichten? [j/N]"
    read -rp "" CONFIRM
    [[ "$CONFIRM" =~ ^[jJyY]$ ]] || { warning "SSL übersprungen. Später nachholen mit: sudo certbot --apache -d $DOMAIN"; }
else
    certbot --apache -d "$DOMAIN" --non-interactive --agree-tos -m "$LE_EMAIL" --redirect
    info "SSL-Zertifikat eingerichtet und HTTP→HTTPS-Weiterleitung aktiv."
fi

# ── .env anlegen ──────────────────────────────────────────────────────────────

step "6/7 – .env konfigurieren"

ENV_FILE="$REPO_PATH/.env"
ENV_EXAMPLE="$REPO_PATH/.env.example"

if [[ -f "$ENV_FILE" ]]; then
    info ".env bereits vorhanden — überspringe."
else
    [[ -f "$ENV_EXAMPLE" ]] || error ".env.example nicht gefunden in $REPO_PATH"

    echo "Bitte Konfiguration eingeben (Enter = Wert aus .env.example übernehmen):"
    echo ""

    _read_env() {
        local key="$1"
        local default
        default=$(grep "^${key}=" "$ENV_EXAMPLE" 2>/dev/null | cut -d'=' -f2- | tr -d '"' || echo "")
        read -rp "  $key [${default:-leer}]: " val
        echo "${key}=${val:-$default}"
    }

    {
        echo "APP_ENV=prod"
        _read_env "FORM_TYPE"
        echo ""
        _read_env "MAIL_HOST"
        _read_env "MAIL_PORT"
        _read_env "MAIL_SECURE"
        _read_env "MAIL_USER"
        _read_env "MAIL_PASS"
        _read_env "MAIL_FROM"
        _read_env "MAIL_FROM_NAME"
        _read_env "MAIL_TO"
    } > "$ENV_FILE"

    # SEPA-Felder nur wenn FORM_TYPE=sepa
    FORM_TYPE_VAL=$(grep "^FORM_TYPE=" "$ENV_FILE" | cut -d'=' -f2)
    if [[ "$FORM_TYPE_VAL" == "sepa" ]]; then
        {
            echo ""
            _read_env "PLACE"
            _read_env "SEPA_CREDITOR_NAME"
            _read_env "SEPA_CREDITOR_ADRESS"
            _read_env "SEPA_CREDITOR_ID"
        } >> "$ENV_FILE"
    fi

    chown "$REAL_USER:www-data" "$ENV_FILE"
    chmod 640 "$ENV_FILE"
    info ".env angelegt."
fi

# ── Composer ──────────────────────────────────────────────────────────────────

step "7/7 – PHP-Abhängigkeiten installieren"

sudo -u "$REAL_USER" composer install --no-dev --optimize-autoloader -d "$REPO_PATH" --quiet \
    --ignore-platform-req=ext-dom \
    --ignore-platform-req=ext-json \
    --ignore-platform-req=ext-libxml \
    --ignore-platform-req=ext-phar \
    --ignore-platform-req=ext-tokenizer \
    --ignore-platform-req=ext-xml \
    --ignore-platform-req=ext-xmlwriter
info "composer install abgeschlossen."

# ── Dateirechte ───────────────────────────────────────────────────────────────

chown -R "$REAL_USER:www-data" "$REPO_PATH"
find "$REPO_PATH" -type d -exec chmod 750 {} \;
find "$REPO_PATH" -type f -exec chmod 640 {} \;
# Skripte ausführbar halten
chmod +x "$REPO_PATH/setup/setup.sh" "$REPO_PATH/setup/update.sh"
info "Dateirechte gesetzt ($REAL_USER:www-data, 750/640)."

# ── Fertig ────────────────────────────────────────────────────────────────────

echo ""
echo -e "${GREEN}${BOLD}╔════════════════════════════════════╗${NC}"
echo -e "${GREEN}${BOLD}║   Setup abgeschlossen!             ║${NC}"
echo -e "${GREEN}${BOLD}╚════════════════════════════════════╝${NC}"
echo ""
echo -e "  Website:  ${BOLD}https://$DOMAIN${NC}"
echo -e "  Repo:     ${BOLD}$REPO_PATH${NC}"
echo ""
echo -e "  Updates später einspielen:"
echo -e "  ${BOLD}cd $REPO_PATH && bash setup/update.sh${NC}"
echo ""
