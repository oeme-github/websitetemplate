# CLAUDE.md – websitetemplate

## Verbindlicher Arbeitsablauf

Der vollständige Arbeitsablauf (Sprache, Startup-Routine, Arbeit im Projekt, Session-End-Routine)
steht **ausschließlich** in `dev-notes/STANDARDS.md` §1–§4 — automatisch per `@`-Import geladen
(siehe „Automatisch geladene Dateien" unten), hier nicht redundant wiederholen. Die Abschnitte
unten enthalten nur **projektspezifische Ergänzungen und Fakten**.

**Rolle dieses Repos:** Template-Ursprung für PHP-One-Pager-Landingpages. Architektur,
Security-Model, Code-Standards, Testing-Aufbau und Dependencies hier sind die **kanonische
Quelle** für alle abgeleiteten Repos (siehe `dev-notes/STANDARDS.md` §3, „Single Source of Truth
für Infra-Fakten" — Kategorie Code-Template-Ableitungen). Bugs/Verbesserungen an diesen Themen
gehören hierher, nicht in ein abgeleitetes Repo.

## Entwicklungsumgebung

Gemeinsame Devbox-Umgebung (OS/Hardware/Migrationsgeschichte): siehe `dev-notes/REPOS.md`
(„Speicherorte") — autoritative Quelle, hier bewusst nicht dupliziert.

- **Projektpfad (Devbox):** `~/git_repos/websitetemplate`
- **Versionskontrolle:** Git, Remote auf GitHub (`github.com/oeme-github/websitetemplate`)
- **Automatisierte Tests** (`composer test`, `npm test`) laufen headless sauber auf der Devbox.
- **Visueller Browser-QA-Workflow:** War bis 2026-09-02 WSL2-gebunden (GUI-Ausnahme, Devbox ist
  reine SSH-Umgebung ohne Browser). Ersatz erfolgreich verprobt (2026-09-03): `php -S
  <devbox-lan-ip>:<port> -t public` auf der Devbox starten, per `mcp__claude-in-chrome` vom
  echten Browser des Users über die LAN-IP ansteuern (Details:
  `dev-notes/projects/websitetemplate.md`, Cross-Machine-Browser-Test-Muster). **Noch offen:**
  saubere URLs (`/impressum` etc., aktuell über `.htaccess`-Rewrites) im Ersatz-Workflow abbilden
  — Router-Skript für `php -S` vs. Apache-Vhost auf der Devbox (bräuchte `sudo`), noch nicht
  entschieden — siehe `BACKLOG.md`. Dieses Kapitel wird final geschrieben, sobald das entschieden
  ist.

### Startup-Routine — projektspezifische Ergänzungen
Generischer Kern: siehe `dev-notes/STANDARDS.md` §2. Keine zusätzlichen Schritte für dieses
Projekt.

## Häufige Befehle
```bash
# PHP-Abhängigkeiten installieren
composer install

# JS-Abhängigkeiten installieren (Jest)
npm install

# PHP-Tests (48 PHPUnit-Tests)
composer test

# JS-Tests (86 Jest-Tests)
npm test
# oder via Composer:
composer test-js

# Einzelne Testdatei
vendor/bin/phpunit tests/IbanValidatorTest.php

# Einzelne Testmethode
vendor/bin/phpunit --filter testValidIbanReturnsTrue
```

### Mailpit – lokale .env (DEV)
```env
APP_ENV=dev
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_SECURE=
MAIL_USER=
MAIL_PASS=
MAIL_FROM=dev@websitetemplate.local
MAIL_FROM_NAME=Dev
MAIL_TO=inbox@websitetemplate.local
```
PHPMailer sendet dann an Mailpit statt an einen echten SMTP-Server. Alle Mails (inkl.
SEPA-PDF-Anhang) sind in dessen Web-UI sichtbar (`http://localhost:8025`).

### Apache-Konfiguration (Referenz, aktuell nicht aktiv genutzt — siehe „Entwicklungsumgebung")
Template: `setup/apache/websitetemplate.conf`

### Template-Einsatz (Referenz)

Dieses Template wird bereits in folgenden Repos eingesetzt (alle per `git remote template`
periodisch nachgezogen, siehe `dev-notes/REPOS.md` „Kontinuierlicher Verbesserungsprozess"):

| Repo | Pfad (Devbox) |
|------|---------------|
| `buero-desk-booking-landing` | `~/git_repos/buero-desk-booking-landing/` |
| `friendsofthehawks` | `~/git_repos/friendsofthehawks/` |
| `beatmungswg-ofterdingen` | `~/git_repos/beatmungswg-ofterdingen/` |

---

## Project Overview

A lightweight, one-page PHP website template with built-in form handling (contact or SEPA). Framework-free, using vanilla HTML/CSS/JavaScript with PHP 8.0+ backend. Security-first design with CSRF, XSS protection, CSP headers, and a RGB-based color scheme system with FOUC prevention.

## Architecture

### Request Flow
All requests route through `public/index.php` (single entry point). Apache `.htaccess` rewrites clean URLs (`/impressum` → `?page=impressum`). Form submissions go to dedicated endpoints: `send_kontakt.php` or `send_sepa.php`.

### Directory Structure
- **`public/`** - Web root. Only this directory is publicly accessible.
- **`src/`** - PHP application layer (PSR-4 autoloaded):
  - `Helpers/` - Utility functions (`clean()`, `maskIBAN()`, `is_dev()`)
  - `Security/` - CSRF tokens, session config, security headers, IBAN validation
  - `Services/` - IBAN bank lookup
  - `http/` - Request/Response wrappers
- **`templates/`** - View layer with `layout/`, `pages/`, and `partials/` subdirectories
- **`content/`** - Markdown and JSON content files (loaded via `$md()` / `$gallery()`)
- **`tests/`** - PHPUnit tests in root; `tests/js/` for Jest tests

### Environment Configuration
Copy `.env.example` to `.env`. Key variables:
- `APP_ENV` - `dev` or `prod` (controls error display)
- `FORM_TYPE` - `contact` or `sepa` (switches form type)
- `MAIL_*` - SMTP settings for PHPMailer (`MAIL_HOST`, `MAIL_PORT`, `MAIL_USER`, `MAIL_PASS`, `MAIL_FROM`, `MAIL_FROM_NAME`, `MAIL_TO`, `MAIL_SECURE`)
- `SEPA_*` - Creditor details for SEPA PDF generation (`SEPA_CREDITOR_NAME`, `SEPA_CREDITOR_ADRESS`, `SEPA_CREDITOR_ID`, `PLACE`)

### Security Model
- CSRF: Session-based tokens with rotation (`src/Security/csrf.php`)
- XSS: Output escaping via `e()` helper, strict CSP headers
- Honeypot: Hidden field for bot detection
- Session: HttpOnly, SameSite=Lax, strict mode
- Headers: Named functions — `setHtmlSecurityHeaders()` in `index.php`, `setApiSecurityHeaders()` in `send_*.php`
- Production: No debug info leaked (controlled by `APP_ENV`)

### Color Scheme System
CSS uses RGB-tuple custom properties per scheme (`[data-color-scheme="..."]`). Three schemes available:
- `default` — blue / gold
- `warm` — red / warm
- `nature` — green

Scheme and theme are persisted in localStorage; `fouc-prevention.js` applies them synchronously before render.

### Code Standards
- PHP: `declare(strict_types=1)` in all files
- JavaScript: Vanilla JS, IIFE pattern, defensive DOM selectors
- CSS: Custom properties for theming, RGB color system, no preprocessors

## Testing

### PHP (PHPUnit) — 48 tests
Tests cover the `src/` directory with 4 test classes:
- `IbanValidatorTest.php` - IBAN checksum validation
- `HelpersTest.php` - Utility function behavior
- `EscaperTest.php` - XSS prevention
- `CsrfTest.php` - Token generation and validation

### JavaScript (Jest) — 86 tests
Tests cover `public/assets/js/` with 9 test files in `tests/js/`:
- `header.test.js` - Header scroll show/hide behaviour
- `theme.test.js` - Dark/light theme toggle and localStorage persistence
- `colorScheme.test.js` - Color scheme selector and localStorage persistence
- `mobileNav.test.js` - Mobile navigation open/close, scroll, hash, and anchor events
- `cookieNotice.test.js` - Cookie notice dismiss, fade-out, and localStorage persistence
- `statsCounter.test.js` - Count-up animation, Intersection Observer, reduced-motion fallback
- `formAjax.test.js` - Form AJAX submission, success/error handling, CSRF rotation, fade-out
- `foucPrevention.test.js` - FOUC prevention script (theme + scheme + cookie state)
- `gallery.test.js` - Image gallery lightbox and tabs: open/close, navigation, keyboard, filtering

Jest environment: jsdom. Run with `npm test`.

### Intentionally Not Tested

| Component | Reason |
|-----------|--------|
| `setBaseSecurityHeaders()` | Simple wrapper; requires PHPUnit process isolation |
| `setHtmlSecurityHeaders()` | Simple wrapper; requires PHPUnit process isolation |
| `setApiSecurityHeaders()` | Simple wrapper; requires PHPUnit process isolation |

**Rationale**: Security header functions are straightforward `header()` calls with no branching logic. CSP misconfigurations surface immediately in browser console. ROI for these tests is low compared to complexity.

## Dependencies

- `vlucas/phpdotenv` - Environment variable loading
- `phpmailer/phpmailer` - SMTP email sending
- `tecnickcom/tcpdf` - SEPA PDF generation
- `erusev/parsedown` - Markdown rendering for content files
- `phpunit/phpunit` (dev) - PHP testing framework
- `jest` + `jest-environment-jsdom` (dev) - JavaScript testing framework

---

## Session-End-Routine — projektspezifische Ergänzungen
Generischer Kern: siehe `dev-notes/STANDARDS.md` §4. Keine zusätzlichen Schritte für dieses
Projekt (der frühere Windows-Mirror-Sync-Schritt ist seit der WSL-Ablösung 2026-09-02
gegenstandslos und entfällt).

## Automatisch geladene Dateien (via `@`-Import)
- @BACKLOG.md — **zuerst lesen**: enthält letzten Stand und wo weitermachen
- @README.md — Projektübersicht und Setup
- @CHANGELOG.md — Versionshistorie und aktuelle Änderungen
- @DESIGN_PATTERN.md — Design-Patterns im Code
- @REVIEW_CHECKLIST.md — Code-Review-Checkliste
- @SECURITY_APPENDIX.md — Security-Implementierungsdetails
- @~/git_repos/dev-notes/STANDARDS.md — verbindlicher, projektübergreifender Arbeitsablauf
  (Hub-Regelwerk; externer Import außerhalb dieses Projekts — Claude Code zeigt beim allerersten
  Laden einen einmaligen Genehmigungsdialog, danach automatisch)

---

## Doku-Check (alle 4 Wochen)
Dedizierte Session zur Synchronisierung der Dokumentation mit dem tatsächlichen Projektstand:
- `CLAUDE.md` — nur noch projektspezifische Fakten hier; „Entwicklungsumgebung" final schreiben,
  sobald die Router-Skript-vs-Apache-Entscheidung gefallen ist
- `README.md` — Features, Konfiguration, Installationsschritte
- `BACKLOG.md` — erledigte Einträge bereinigen, neue Erkenntnisse ergänzen; IDs auf
  `<repo>_<ID>`-Konvention prüfen und ggf. nachziehen (siehe `dev-notes/STANDARDS.md`) — inkl.
  Querverweise in `dev-notes/PROJECTS.md`/`dev-notes/projects/websitetemplate.md` und den
  abgeleiteten Repos

Nächster Doku-Check: **2026-10-03**

---

## Verwandte Repositories

| Repo | Zweck |
|------|-------|
| `oeme-github/dev-notes` | PM-Hub, Projektübersicht |
| `oeme-github/buero-desk-booking-landing` | Abgeleitetes Repo (Downstream-Klon) |
| `oeme-github/friendsofthehawks` | Abgeleitetes Repo (Downstream-Klon) |
| `oeme-github/beatmungswg-ofterdingen` | Abgeleitetes Repo (Downstream-Klon) |
