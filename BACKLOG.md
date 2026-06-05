# Backlog – One-Pager Website Template

## Letzter Stand

**Version:** v1.6.1 (gepusht)  
**Zuletzt abgeschlossen:** Issues #7, #8, #13 — Rate Limiting, IBAN DSGVO-Hinweis, update.sh fix

### Abgeschlossen in dieser Session

**Issue #13 — `update.sh`: git pull ohne Tracking-Branch**
- `setup/update.sh` — `git pull` → `git pull origin main`

**Issue #8 — IBAN UX-Verbesserung (DSGVO)**
- `templates/partials/forms/sepa.php` — Hinweistext unter IBAN-Feld (openiban.com)
- `public/assets/css/main.css` — `.form-hint`-Klasse ergänzt
- `content/legal/datenschutz.md` — vollständiger Mustertext mit allen template-spezifischen Abschnitten (Session-Cookie, Kontaktformular, SEPA, openiban.com, Betroffenenrechte)
- `README.md` — Hinweis auf vorausgefüllten Datenschutz-Mustertext

**Issue #7 — Rate Limiting für Formulare**
- Bereits implementiert in `src/http/FormEndpoint.php` — Issue als erledigt geschlossen

---

### Abgeschlossen in vorherigen Sessions

**Issue #12 — `{{VAR_NAME}}`-Platzhalter in `$md()`**
- `public/index.php` — `$md()` führt nach Markdown-Rendering denselben Placeholder-Replace-Pass durch wie `$gallery()`

**Paket D1–D3** — Content-Infrastruktur, A11y, SEPA

**Paket E — Templates/CSS/htaccess**
- `impressum.php` + `datenschutz.php` → `$md()`, LCP-Preload, Footer „Start"-Link
- CSS 600px-Breakpoint (Gallery 1-col, Lightbox nav ausblenden)
- `.htaccess` Gzip + Cache-Control

**Paket F — Hintergrundbilder + CSS-Tokens**
- `body_bg.jpg` Placeholder, `--bg-image-*`, `--color-text-hero`, `--font-display`
- body/section.alt/hero mit Hintergrundbildern + Overlays
- Dark-Mode Overlays für alle Sektionen
- Gallery 900px→2-col Breakpoint, MAIL_FROM_NAME aus .env

**Testzahlen gesamt:**
- PHP Unit: 55 Tests · JS Jest: 101 Tests · Integration: 7 Tests

---

## Offene Issues (GitHub)

| # | Titel | Priorität | Status |
|---|-------|-----------|--------|
| #1 | SEPA-Flow rechtlich finalisieren | Offen | Wartet auf Juristencheck |
| #5 | Logging-Strategie (DEV vs PROD) | Offen | Wartet auf Entscheidung |

---

## Zurückgestellt

- **Screenreader-Test SEPA-Formular**: manueller Test (NVDA/VoiceOver) — kein Code-Task
- **Issue #1** (SEPA rechtlich): Wartet auf Juristencheck / Pflichttexte
- **Issue #5** (Logging): Wartet auf Entscheidung zu Request-ID
