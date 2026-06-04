# One-Pager Website Template

Solider Ausgangspunkt für eine einfache, barrierearme One-Page-Website mit Kontakt- oder SEPA-Formular. Framework-frei, PHP 8+, Security by default.

> Nach eigenen Anpassungen (Farben, Schriften, Inhalte) sollte die Barrierefreiheit erneut geprüft werden.

---

## Schnellstart: Von Template zur laufenden Seite

### 1. Neues Repo anlegen

Auf GitHub oben rechts **„Use this template"** klicken → „Create a new repository".  
Das erzeugt ein eigenes Repo mit dem Template-Code — kein Fork, eigene History.

```bash
# Neues Repo lokal klonen
git clone git@github.com:<user>/<neues-repo>.git
cd <neues-repo>
```

### 2. Lokal einrichten

```bash
# PHP-Abhängigkeiten
composer install

# JS-Abhängigkeiten (nur für Tests)
npm install

# .env anlegen
cp .env.example .env
# .env öffnen und ausfüllen (APP_ENV=dev, MAIL_*, FORM_TYPE)
```

Lokaler Webserver (Apache mit `mod_rewrite`) muss auf das `public/`-Verzeichnis zeigen.  
Für lokalen Mailversand empfiehlt sich [Mailpit](https://mailpit.axllent.org/) (`MAIL_HOST=127.0.0.1`, `MAIL_PORT=1025`).

### 3. Anpassen

| Was | Wo |
|-----|----|
| Texte & Inhalte | `content/home/` (Markdown), `templates/pages/home.php` |
| Impressum / Datenschutz | `content/legal/impressum.md`, `content/legal/datenschutz.md` |
| Copyright-Inhaber (Footer) | `content/legal/copyright.json` → `owner` |
| Galerie-Bilder | `public/assets/images/content/`, `content/home/gallery.json` |
| Hero-Bild | `public/assets/images/hero/` |
| Logo | `public/assets/logo/` |
| Farben & Farbschema | `public/assets/css/main.css` → `[data-color-scheme="..."]`-Blöcke |
| Formulartyp (Kontakt / SEPA) | `.env` → `FORM_TYPE=contact` oder `FORM_TYPE=sepa` |
| SEPA-Gläubigerdaten | `.env` → `SEPA_CREDITOR_*`, `PLACE` |
| Sections aktivieren / deaktivieren | `.env` → `SECTION_HERO`, `SECTION_GALLERY`, `SECTION_STATS`, `SECTION_ABOUT`, `SECTION_CONTACT` |
| Favicon & App-Icons | `public/assets/icons/` |

Welche Dateien zum Template gehören und welche pro Deployment angepasst werden → **[Template vs. kundenspezifisch](#template-vs-kundenspezifisch)**.

### 4. Deployen

Für die Ersteinrichtung auf einem VPS → **[DEPLOY.md](DEPLOY.md)**

```bash
# Auf dem Server (einmalig):
sudo bash setup/setup.sh
```

Das Skript installiert alle Abhängigkeiten, richtet Apache und SSL ein und führt durch die `.env`-Konfiguration.

### 5. Weiterentwickeln

```
lokal entwickeln → testen → git push → auf dem Server: bash setup/update.sh
```

```bash
# Änderungen auf den Server bringen:
ssh user@server
cd /var/www/<domain>
bash setup/update.sh
sudo systemctl reload apache2
```

---

## Template vs. kundenspezifisch

**Faustformel:** `content/`, `public/assets/images/`, `public/assets/logo/`, `public/assets/icons/` und `.env` sind Kundensache. Alles andere gehört dem Template.

### Kundenspezifisch — wird pro Deployment angepasst

| Bereich | Dateien |
|---------|---------|
| Texte | `content/home/*.md` — hero, about, features, stats, contact |
| Daten | `content/home/*.json` — gallery, stats, about-cards, topbar-links, videos |
| Rechtliches | `content/legal/impressum.md`, `datenschutz.md`, `copyright.json` |
| Bilder | `public/assets/images/` — hero, content, background |
| Logo | `public/assets/logo/` |
| Favicon & App-Icons | `public/assets/icons/` |
| Konfiguration | `.env` — APP_ENV, FORM_TYPE, SECTION_*, MAIL_*, SEPA_* |

### Template — nicht anfassen

| Bereich | Dateien |
|---------|---------|
| PHP-Logik | `src/` komplett — Security, CSRF, IBAN, Helpers, HTTP-Layer |
| Routing | `public/index.php` |
| JS-Module | `public/assets/js/` |
| Layout | `templates/layout/base.php`, `templates/partials/` |
| Seitenstruktur | `templates/pages/home.php` — Reihenfolge und Struktur der Sections |
| Formular-HTML | `templates/partials/forms/contact.php`, `sepa.php` |
| CSS — Komponenten | `public/assets/css/main.css` (Komponenten-Styles) |
| Schriften | `public/assets/css/fonts.css`, `public/assets/fonts/` |
| Server | `public/.htaccess`, `setup/` |
| Tests | `tests/` |

### Partiell anpassbar

| Was | Regel |
|-----|-------|
| `main.css` — `[data-color-scheme="..."]`-Blöcke | RGB-Primitive für eigene Farbschemata austauschen ist vorgesehen; Komponenten-CSS nicht anfassen |
| `templates/pages/home.php` | Sections per `.env` (`SECTION_*`) steuern; Reihenfolge oder neue Sections ändern ist Template-Eingriff |

### Template-Updates übernehmen

Kundenprojekte können Verbesserungen aus dem Template per Git-Merge übernehmen. Weil Template- und Kundendateien sich nicht überschneiden, entstehen dabei in der Regel keine Konflikte.

**Einmalige Einrichtung** (im Kundenprojekt):

```bash
git remote add template https://github.com/oeme-github/websitetemplate.git
git fetch template
```

Beim ersten Merge ist `--allow-unrelated-histories` nötig, weil "Use this template" keine gemeinsame Git-History erzeugt:

```bash
git merge template/main --allow-unrelated-histories
```

**Folge-Updates** (ab dem zweiten Mal):

```bash
git fetch template
git merge template/main
```

**Bei Konflikten:** Konflikte können nur in den "partiell anpassbaren" Dateien auftreten (`main.css`-Farbblöcke, `home.php`-Struktur). Kundendateien in `content/`, `public/assets/images/` und `.env` werden vom Merge nie berührt.

---

## Features

- Responsive One-Pager mit barrierefreiem Desktop- & Mobile-Menü
- Scroll-abhängiger Header, Footer fixiert am unteren Rand
- **FOUC Prevention** — kein Theme-/Farbschema-Flash beim Laden
- **Color Scheme System** — 3 umschaltbare Farbschemata (Default, Warm, Nature)
- **Cookie Notice** — DSGVO-konformer Hinweis mit Dismiss
- **Stats Counter** — Count-Up-Animation beim Scrollen in die Sektion
- **Image Gallery + Lightbox** — Keyboard-Navigation, Focus-Trap, Kategorie-Tabs
- **Kontaktformular** oder **SEPA-Formular** (umschaltbar per `.env`)
- CSRF- & Honeypot-Schutz, AJAX-Submit mit ARIA-Feedback
- Legal-Pages (Impressum, Datenschutz)
- Rate Limiting für Formular-Endpoints (Session-basiert)
- IBAN Live-Validierung mit Bankname-Lookup (SEPA)

---

## Formular-Typen

```env
FORM_TYPE=contact   # Klassisches Kontaktformular
FORM_TYPE=sepa      # SEPA-Lastschriftmandat mit PDF-Anhang
```

Das SEPA-Formular erzeugt automatisch ein PDF-Mandat und versendet es per E-Mail.  
IBAN-Prüfziffer wird serverseitig validiert; Bankname wird live per API nachgeladen.

---

## Color Scheme System

RGB-basierte CSS Custom Properties, drei Schemas vorkonfiguriert:

| Schema | Farben |
|--------|--------|
| `default` | Blau / Gold |
| `warm` | Rot / Warm |
| `nature` | Grün |

Eigenes Schema: neuen `[data-color-scheme="..."]`-Block in `main.css` mit RGB-Tupel-Variablen ergänzen.

---

## Content-Management

Texte als Markdown, Galerien als JSON im `content/`-Verzeichnis:

```
content/
├── home/
│   ├── hero.md
│   ├── about.md
│   └── gallery.json
└── legal/
    ├── impressum.md
    └── datenschutz.md
```

In Templates: `$md('home/hero')` und `$gallery('home/gallery')`.

---

## Lokale Entwicklung & Tests

```bash
composer test        # PHPUnit — 55 Unit-Tests
npm test             # Jest   — 101 JS-Tests
composer test-integration  # E2E via HTTP + Mailpit (Apache muss laufen)
```

Kein Build-Step erforderlich — CSS und JS werden direkt ausgeliefert.

---

## Technik

- HTML5 / CSS3 / Vanilla JavaScript
- PHP ≥ 8.0, Composer
- Apache mit `mod_rewrite`
- PHPMailer, TCPDF, Parsedown
- PHPUnit 10, Jest 29
- Lighthouse-Referenzwerte: Performance ~98, Accessibility 100, Best Practices 100, SEO 100

---

## Philosophy

- Barrierefreiheit ist Teil der Architektur
- Einfachheit vor Komplexität — kein Framework-Zwang
- Security by default
- Testbarkeit von Anfang an

**Nicht-Ziele:** Kein CMS, kein Full-Framework, keine Garantie für vollständige WCAG-Konformität nach individuellen Anpassungen.

---

## Lizenz

[EUPL v1.2](LICENSE) — Autor: Jörg Römhild
