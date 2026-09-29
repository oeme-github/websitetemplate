## v1.7.0 – Site-Identität, Dev-Router, Template-Fixes

### Added
- **Site-Identität konfigurierbar** (`websitetemplate_D03`): `content/site.json` (Fallback
  `content/site.example.json`) steuert Name (H1 der Startseite), Topbar-Text (`tagline`),
  Logo-Alt-Text, Seitentitel (`titles.home/impressum/datenschutz/404`), Meta-Descriptions pro
  Route (`metaDescriptions.<page>`) und Navigationslabels (`nav.hero/gallery/stats/about/contact`).
  Fehlende Schlüssel fallen auf die bisherigen Werte zurück — ohne `site.json` bleibt alles wie in
  v1.6.x.
- **`dev/router.php`**: Router für den eingebauten PHP-Server, bildet die Regeln aus
  `public/.htaccess` nach (saubere URLs, 403 für nicht freigegebene `.php`-Dateien und Dotfiles).
  Start: `php -S <host>:<port> -t public dev/router.php`. Setzt die Entscheidung aus
  `websitetemplate_D01` um (Router-Skript statt Apache-Vhost für die lokale Entwicklung).

### Changed
- `CLAUDE.md` „Entwicklungsumgebung" final: `php -S` auf Port 8011 mit `dev/router.php`, Apache
  nur noch als Produktions-Referenz; `websitetemplate_D01` damit abgeschlossen

### Fixed
- `.visually-hidden` fehlte in `main.css` → Zahlen im Stats-Bereich erschienen doppelt
  (`websitetemplate_B01`)
- Bilder in Markdown-Inhalten sprengten die Seitenbreite → globale `img`-Regel
  `max-width: 100%` (`websitetemplate_B02`)
- Hero-Gradient war deckend, `--bg-image-hero` nie sichtbar → `--color-bg-hero-start/-end` jetzt
  `rgba(..., 0.75)`, Downstream kann den Wert über die Tokens überschreiben (`websitetemplate_B03`)
- `site.webmanifest` verwies auf `/android-chrome-*.png` statt `/assets/icons/…`
  (`websitetemplate_B04`)

Gemeldet von `beatmungswg-ofterdingen` (`beatmungswg-ofterdingen_B08`).

### Migration für Downstream-Repos
Wer bisher `templates/partials/header.php` oder `public/index.php` lokal gepatcht hat, um Name,
Topbar-Text, Logo-Alt, Titel, Meta-Description oder Menütexte zu ändern:
1. `git merge template/main` — bei Konflikten in diesen beiden Dateien die **Template-Version**
   übernehmen (`git checkout --theirs templates/partials/header.php public/index.php`).
2. `cp content/site.example.json content/site.json` und die bisher gepatchten Texte dort
   eintragen (nur abweichende Schlüssel nötig).
3. Prüfen, dass `content/site.json` getrackt wird (Override `!content/**/*.json` in der eigenen
   `.gitignore`, siehe README „Template-Updates übernehmen").

Alle Template-Änderungen seit v1.6.0 gehören zu diesem Release, auch der
`content/`-`.example.*`-Mechanismus samt `.gitignore`-Änderung.

---

## v1.6.0 – Section Flags

### Added
- **Section Flags**: Jede Hauptsektion der Startseite ist einzeln über `.env` de-/aktivierbar (`SECTION_HERO`, `SECTION_GALLERY`, `SECTION_STATS`, `SECTION_ABOUT`, `SECTION_CONTACT`)
- `$section()`-Closure in `index.php` — liest `SECTION_*` aus `.env`, Default ist `true` (aktiviert)
- `.env.example` dokumentiert alle fünf Flags

### Design-Entscheidungen
- Default `true`: Deployments ohne explizite Flags verhalten sich unverändert
- Lightbox wird zusammen mit `SECTION_GALLERY` ein-/ausgeblendet (logische Einheit)
- Flags steuern nur Rendering — kein JS, kein CSS-Aufwand für deaktivierte Sections

---

## v1.3.0 – Color Schemes, Cookie Notice, Gallery, Stats & Testing

### Added
- **Color Scheme System**: RGB-basierte CSS Custom Properties mit 3 austauschbaren Schemas (Default, Warm, Nature)
- **FOUC Prevention**: `fouc-prevention.js` verhindert Theme-/Schema-Flash beim Laden
- **Cookie Notice**: DSGVO-konformer Cookie-Hinweis mit Dismiss-Button und localStorage-Persistenz
- **Color Scheme Selector**: `<select data-color-scheme-select>` im Footer, persistent via localStorage
- **Stats Counter**: Count-Up-Animation mit Intersection Observer und `prefers-reduced-motion`-Support
- **Image Gallery + Lightbox**: Vollständige Galerie-Komponente mit Keyboard-Navigation, Focus-Trap und Tabs
- **Gallery Tabs**: Dynamischer Kategoriefilter aus `data-gallery-category` Attributen
- **Content-Management**: Parsedown-Dependency + `$md()` / `$gallery()` Loader in `index.php`; `content/`-Verzeichnis mit Placeholder-Dateien
- **Testing-Infrastruktur**: PHPUnit 10 (48 Tests) + Jest 29 (86 Tests); `phpunit.xml`, `package.json`
- Favicon-Links und LCP-Preloads in `base.php`

### Changed
- **Security Headers**: `headers.php` refaktoriert zu `setBaseSecurityHeaders()`, `setApiSecurityHeaders()`, `setHtmlSecurityHeaders()`
- `index.php`: ruft `setHtmlSecurityHeaders()` als erste Aktion auf
- `send_kontakt.php` / `send_sepa.php`: rufen `setApiSecurityHeaders()` als erste Aktion auf
- `.env.example`: SMTP_* → MAIL_*, neue Variable `MAIL_TO`, Typo-Fixes
- Form-Erfolgsmeldung faded nach 5 Sekunden automatisch aus
- CSS Custom Properties auf RGB-Tupel-System umgestellt (Alpha-Support)

### Quality
- Alle bestehenden Features erhalten (kein Breaking Change für Browser-Nutzer)
- `.env`-Nutzer: `SMTP_*` → `MAIL_*` umbenennen (Breaking)

---

## v1.2.1 – SEPA Form Layout & UX Fixes

### Fixed
- Incorrect desktop layout of SEPA form fields
- Misaligned select fields due to nested label/select markup
- Inconsistent consent checkbox alignment in SEPA form

### Improved
- Unified form field structure between contact and SEPA forms
- Robust grid behavior for desktop view without layout side effects
- Consent checkboxes now stack vertically and behave consistently across viewports

### Quality
- No breaking changes
- No JavaScript changes required
- Accessibility preserved (label wrapping, focus order, click targets)

---

## v1.2.0 – Forms, Security & UX Finalization

### Added
- Optionales **SEPA-Formular** als Alternative zum Kontaktformular
  - Umschaltbar über `.env` (`FORM_TYPE=contact|sepa`)
  - Gemeinsame Formular-Architektur mit austauschbaren Partials
- SEPA-Mailversand mit:
  - serverseitiger Validierung
  - IBAN-Prüfung
  - automatisch generiertem **SEPA-PDF** als E-Mail-Anhang
- Zentrale Environment-Steuerung (DEV / PROD) über `APP_ENV`
- Einheitliche JSON-Response-Architektur für alle Formular-Endpunkte

### Improved
- UX-Feinschliff für Header & Navigation:
  - Header blendet sich beim Scrollen aus/ein (scroll down / up)
  - Mobile Menü schließt zuverlässig bei:
    - Scroll
    - Seitenwechsel
    - Navigation über Logo/Home-Link
- Footer dauerhaft am unteren sichtbaren Rand fixiert
- Formular-UX:
  - konsistentes ARIA-Feedback bei Fehlern & Erfolg
  - robuster AJAX-Flow mit klarer Fehlerbehandlung
- Klarere Trennung von:
  - Routing
  - Layout
  - Formular-Logik
  - Business-Logik (Mail, PDF)

### Security
- Vereinheitlichte Security-Baseline:
  - CSRF-Schutz mit Token-Rotation
  - Honeypot-Mechanismus
  - Whitelist-basiertes Routing
  - konsistente HTTP-Statuscodes
- Globale Error- & Exception-Handler für API-Endpunkte
- Kein Information Leakage in Produktionsumgebung

### Quality
- Stabiler Formular-Flow für Kontakt **und** SEPA
- Alle bekannten Edge-Cases im Mobile-Menü behoben
- Codebasis bereinigt und dokumentiert
- Keine Breaking Changes für bestehende Nutzer

---

**Status:** Stable

---

## v1.1.0 – Legal Pages & Layout Refinement

### Added
- New legal pages: Impressum and Datenschutzerklärung
- Consistent global header and footer across all pages
- Footer redesigned using responsive CSS Grid

### Improved
- Legal content layout with dedicated `.section-legal`
- Removed scroll-snap from legal pages
- Improved mobile readability for long headings
- Reduced footer height on mobile
- Mobile viewport handling using modern `svh/dvh`

### Quality
- Lighthouse: 100 / 100 / 100 / ~98
- No breaking changes

---

## v1.0.0 – Initial Release

Erste stabile Version des One-Pager-Templates.

### Highlights
- Barrierearmer One-Pager mit klarer HTML-Struktur
- Responsive Header mit Desktop- & Mobile-Navigation
- Barrierefreies Mobile-Menü mit korrektem ARIA-State
- Kontaktformular mit:
  - CSRF-Schutz
  - Honeypot gegen Bots
  - serverseitiger Validierung
  - AJAX-Submit mit ARIA-Feedback
- Dark-/Light-Theme über CSS Custom Properties
- Saubere Trennung von HTML, CSS, JavaScript und PHP

### Qualität & Standards
- Lighthouse:
  - Performance: 98
  - Accessibility: 100
  - Best Practices: 100
  - SEO: 100
- Frameworkfreies Setup (Vanilla JS / PHP)
- PHP ≥ 8 mit Composer (PHPMailer)
- Sichere PHP-Endpunkte via `.htaccess`

### Dokumentation
- Vollständige README mit Deployment-Hinweisen
- CONTRIBUTING.md für externe Beiträge
- MIT License

### Hinweise
Dieses Release dient als stabiler Ausgangspunkt.
Anpassungen an Design, Inhalt oder Struktur sollten stets mit einer erneuten Accessibility-Prüfung einhergehen.

---

**Status:** Stable
