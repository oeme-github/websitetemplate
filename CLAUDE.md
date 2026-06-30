# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Startup

> Claude Code wird aus WSL2 gestartet: `cd ~/git_repos/websitetemplate && claude`

The following files are automatically loaded at startup (via `@` import):
- @BACKLOG.md - **Check first**: Contains last session status and where to continue work
- @README.md - Project overview and setup instructions
- @CHANGELOG.md - Version history and recent changes
- @DESIGN_PATTERN.md - Design patterns used in the codebase
- @REVIEW_CHECKLIST.md - Code review checklist
- @SECURITY_APPENDIX.md - Security implementation details

## Session End

Before ending a session:

1. Update `BACKLOG.md` with current status, what was completed, where to continue
2. Commit and push all open changes:
   ```bash
   git add -p
   git commit -m "..."
   git push origin main
   ```
3. Windows-Kopie synchronisieren (WSL2 ist primär — Windows zieht nach):
   ```bash
   git -C /mnt/f/git_repos/websitetemplate pull
   ```
4. Sage **"Session beenden"** im Hub (dev-notes) — Claude erstellt dort die Übergabe-Notiz.

Das stellt sicher dass `F:\git_repos\websitetemplate` auf Windows immer aktuell ist.

## Project Overview

A lightweight, one-page PHP website template with built-in form handling (contact or SEPA). Framework-free, using vanilla HTML/CSS/JavaScript with PHP 8.0+ backend. Security-first design with CSRF, XSS protection, CSP headers, and a RGB-based color scheme system with FOUC prevention.

## Commands

```bash
# Install PHP dependencies
composer install

# Install JS dependencies (Jest)
npm install

# Run PHP tests (48 PHPUnit tests)
composer test

# Run JS tests (86 Jest tests)
npm test
# or via Composer:
composer test-js

# Run PHPUnit directly
vendor/bin/phpunit

# Run a single test file
vendor/bin/phpunit tests/IbanValidatorTest.php

# Run a specific test method
vendor/bin/phpunit --filter testValidIbanReturnsTrue
```

## Local Environment (WSL2)

Die primäre Entwicklungsumgebung ist **WSL2 (Ubuntu)**. VS Code verbindet sich per Remote-WSL direkt in das WSL2-Dateisystem. Alle Befehle (PHP, Composer, npm, Git, Apache) laufen in WSL2.

- **OS**: Ubuntu on WSL2
- **Server**: Apache2 with `mod_rewrite` enabled
- **PHP**: 8.x (installed via apt)
- **Web root**: `public/` directory — Apache VirtualHost points here
- **Dev repo**: `/home/oeme/git_repos/websitetemplate/`
- **No build step** — CSS and JS are served as-is
- URL rewrites via `.htaccess` work out of the box
- **GitHub CLI**: `gh` (on PATH in WSL2)

### Session-Start (täglich)

```bash
# 1. In WSL2-Terminal:
cd ~/git_repos/websitetemplate

# 2. Apache läuft? (WSL2 startet Dienste nicht automatisch)
sudo service apache2 status
sudo service apache2 start   # falls nicht aktiv

# 3. Mailpit starten (lokaler SMTP-Catcher für Formular-Tests):
mailpit &
# → SMTP auf localhost:1025 (kein TLS, kein Auth)
# → Web-UI: http://localhost:8025

# 4. VS Code mit Remote-WSL öffnen:
code .
# → VS Code öffnet sich verbunden mit WSL2
# → Terminal in VS Code ist direkt das WSL2-Terminal
```

Browser-Test (von Windows): `http://websitetemplate.local`  
Mail-Inbox (lokal): `http://localhost:8025`

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

PHPMailer sendet dann an Mailpit statt an einen echten SMTP-Server.
Alle Mails (inkl. SEPA-PDF-Anhang) sind in der Web-UI sichtbar.

### Einmaliges Setup (WSL2-Seite)

```bash
# Traversal-Rechte für www-data (WSL2-spezifisch, sonst 403):
chmod o+x /home/oeme
chmod o+x /home/oeme/git_repos

# Apache-Konfiguration:
sudo cp setup/apache/websitetemplate.conf /etc/apache2/sites-available/
sudo a2ensite websitetemplate.conf
sudo a2enmod rewrite
sudo service apache2 restart

# Abhängigkeiten:
composer install
npm install
```

### Einmaliges Setup (Windows-Seite)

**`C:\Users\jroem\.wslconfig`** (WSL2 Mirrored Networking — damit `localhost` von Windows auf WSL2 zeigt):
```ini
[wsl2]
networkingMode=mirrored
```
Nach Änderung: `wsl --shutdown` in PowerShell, dann WSL2 neu starten.

**`C:\Windows\System32\drivers\etc\hosts`** (einmalig, als Admin):
```
127.0.0.1 websitetemplate.local
```

### Apache-Konfiguration

Template: `setup/apache/websitetemplate.conf`
Auf dem Server: `/etc/apache2/sites-available/websitetemplate.conf`

### Template-Einsatz (Referenz)

Dieses Template wird bereits in folgenden Projekten eingesetzt:

| Projekt | Pfad (WSL2) | URL (lokal) |
|---------|-------------|-------------|
| friendsofthehawks | `/home/oeme/git_repos/friendsofthehawks/` | `http://friendsofthehawks.localhost` |
| beatmungswg-ofterdingen | `/home/oeme/git_repos/beatmungswg-ofterdingen/` | `http://beatmungswg-ofterdingen.local` |

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
