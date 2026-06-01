# Backlog – One-Pager Website Template

## Letzter Stand

**Version:** v1.5.0 + Pakete D–F + Bugfixes (gepusht)  
**Zuletzt abgeschlossen:** Umfassender friendsofthehawks-Abgleich

### Abgeschlossen in dieser Session

**Paket D1–D3** (vorherige Session) — Content-Infrastruktur, A11y, SEPA

**Paket E — Templates/CSS/htaccess**
- `impressum.php` + `datenschutz.php` → `$md()`, LCP-Preload, Footer „Start"-Link
- CSS 600px-Breakpoint (Gallery 1-col, Lightbox nav ausblenden)
- `.htaccess` Gzip + Cache-Control

**Paket F — Hintergrundbilder + CSS-Tokens**
- `body_bg.jpg` Placeholder, `--bg-image-*`, `--color-text-hero`, `--font-display`
- body/section.alt/hero mit Hintergrundbildern + Overlays
- Dark-Mode Overlays für alle Sektionen
- Gallery 900px→2-col Breakpoint, MAIL_FROM_NAME aus .env

**Bugfixes dieser Session**
- WebP-Versionen der 4 Placeholder-Bilder (404-Fehler in Galerie)
- Hamburger-Menü global (kein Desktop-Nav mehr)
- Footer nicht mehr `position: fixed` — scrollt mit, Layout-Padding entfernt
- Redundantes Label im Footer entfernt
- Hero-Bild Preload entfernt (CSS `var()` nicht vom Preload-Scanner erkennbar)

**Testzahlen gesamt:**
- PHP Unit: 55 Tests · JS Jest: 101 Tests · Integration: 7 Tests

---

## Nächste Session: Paket E — Abgleich friendsofthehawks (Rest)

### Paket F — Hintergrundbilder + fehlende CSS-Tokens + MAIL_FROM_NAME ✅ abgeschlossen

- [x] `public/assets/images/background/body_bg.jpg` — Placeholder (ImageMagick)
- [x] CSS `:root` — `--bg-image-hero`, `--bg-image-body`, `--color-text-hero`, `--font-display`
- [x] CSS `body` — Hintergrundbild mit Overlay, doppelter Block entfernt
- [x] CSS `.section.alt` — Hintergrundbild mit Overlay
- [x] CSS `.hero` — `flex`-Layout, Hintergrundbild aktiv, `color: var(--color-text-hero)`
- [x] CSS Dark-Mode — Overlays für body, header, nav-mobile, section.alt, footer, #contact
- [x] CSS — Gallery 900px → 2-Spalten Breakpoint (war fälschlicherweise bei 768px)
- [x] `send_kontakt.php` + `send_sepa.php` — `MAIL_FROM_NAME` aus `.env`

### Paket E — Abgleich friendsofthehawks ✅ abgeschlossen

- [x] `impressum.php` + `datenschutz.php` → `$md('legal/...')`, kein Hardcode mehr
- [x] `base.php` → LCP-Preload für Hero-Bild + Playfair Display 700
- [x] `footer.php` → „Start"-Link in Legal-Nav
- [x] `main.css` → `@media (max-width: 600px)`: Gallery 1-Spalte + Lightbox Nav ausblenden
- [x] `.htaccess` → Gzip-Kompression + Cache-Control für statische Assets

---

## Offene Issues (GitHub)

### Paket D2 — Accessibility-Fixes ✅ abgeschlossen

- [x] `header.php` doppelte Security-Header entfernt (D1)
- [x] `header.php` `aria-hidden` + `tabindex` von `.nav-mobile` entfernt (D1)
- [x] `.lightbox` CSS `visibility: hidden` (war bereits vorhanden)
- [x] Lightbox-HTML kein hardcodiertes `aria-hidden` (D1)
- [x] `.nav-mobile` CSS `visibility: hidden` + Transition-Timing

### Paket D3 — SEPA-Verbesserungen ✅ abgeschlossen

- [x] Tippfehler `Qurtal` → `Quartal` (Formular + Whitelist)
- [x] Neue Felder: `geburtsdatum` (Pflicht, type=date), `telefon` + `herkunft` (optional)
- [x] `send_sepa.php` — `geburtsdatum` einlesen, YYYY-MM-DD → DD.MM.YYYY, `checkdate()` Validation
- [x] `SepaPdf.php` — Felder maschinenlesbar, eigene Sections (Antragsteller/Mitgliedschaft/Bank/Mandat)
- [x] `SepaPdf.php` — dynamischer Titel (Spende vs. Mitgliedsantrag), XSS-Escaping via `esc()`
- [x] `send_sepa.php` — dynamischer E-Mail-Betreff
- [x] `send_sepa.php` — PDF-Dateiname `{mandateId}.pdf`
- [x] Integrations-Test aktualisiert (neues Pflichtfeld, Betreff, Dateinamen-Pattern)

---

## Offene Issues (GitHub)

| # | Titel | Priorität | Status |
|---|-------|-----------|--------|
| #1 | SEPA-Flow rechtlich finalisieren | Offen | Wartet auf User-Input |
| #5 | Logging-Strategie (DEV vs PROD) | Offen | Wartet auf User-Input |

---

## Zurückgestellt

- **Screenreader-Test SEPA-Formular**: manueller Test (NVDA/VoiceOver) — kein Code-Task
- **Issue #1** (SEPA rechtlich): Wartet auf Juristencheck / Pflichttexte
- **Issue #5** (Logging): Wartet auf Entscheidung zu Request-ID
