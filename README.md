# One-Pager Website Template

Diese One-Page-Website ist als **Template** gedacht.
Sie bietet einen soliden Ausgangspunkt für eine einfache, barrierearme Website mit mehreren Sektionen und Formularen.

Das Template erhebt **keinen Anspruch auf Vollständigkeit**, legt jedoch Wert auf:
- saubere Struktur
- gute Wartbarkeit
- Barrierefreiheit (WCAG-orientiert)
- Security by default

⚠️ Hinweis:
Nach eigenen Anpassungen (z. B. Farben, Schriften, Inhalte) sollte die Barrierefreiheit erneut geprüft werden.

---

## Features

- Responsive One-Pager
- Barrierefreies Desktop- & Mobile-Menü
- Scroll-abhängiger Header (hide on scroll down / show on scroll up)
- Footer fixiert am unteren sichtbaren Rand
- **FOUC Prevention** – kein Theme-/Farbschema-Flash beim Laden
- **Color Scheme System** – 3 umschaltbare Farbschemata (Default, Warm, Nature), persistent via localStorage
- **Cookie Notice** – DSGVO-konformer Hinweis mit Dismiss (localStorage)
- **Stats Counter** – Count-Up-Animation beim Scrollen (`data-count-target`)
- **Image Gallery + Lightbox** – mit Keyboard-Navigation, Focus-Trap und Kategorie-Tabs
- Formular-System:
  - Kontaktformular **oder**
  - alternatives **SEPA-Formular**
- Umschaltbares Formular über `.env`
- CSRF- & Honeypot-Schutz
- AJAX-Submit mit ARIA-Feedback (Erfolgsmeldung faded nach 5 s)
- Legal-Pages für Impressum und Datenschutz

---

## Technik

- HTML5 / CSS3
- Vanilla JavaScript
- PHP ≥ 8.0
- Composer (PHPMailer, TCPDF, Parsedown)
- Testing: PHPUnit 10 + Jest 29
- Konfiguration über `.env`
- Lighthouse (Referenzwerte):
  - Performance: ~98
  - Accessibility: 100
  - Best Practices: 100
  - SEO: 100

---

## Formular-Typen

Das verwendete Formular kann über die `.env` gesteuert werden:

```env
FORM_TYPE=contact
# oder
FORM_TYPE=sepa
```

### Kontaktformular
- Klassisches Kontaktformular
- Serverseitige Validierung
- Mailversand via SMTP

### SEPA-Formular
- Erweiterte Felder (Adresse, IBAN, Beitrag, Rhythmus)
- IBAN-Validierung
- Automatische Generierung eines **SEPA-PDF**
- PDF wird als E-Mail-Anhang versendet
- Elektronische Mandatserteilung (ohne Unterschrift)

---

## Color Scheme System

Das CSS nutzt RGB-basierte Custom Properties. Drei Schemas sind vorkonfiguriert:

| Schema | Beschreibung |
|--------|-------------|
| `default` | Blau / Gold (Standard) |
| `warm` | Rot / Warm |
| `nature` | Grün |

Schemas werden per `<select data-color-scheme-select>` im Footer umgeschaltet und via `localStorage` gespeichert. Der aktive Wert wird im `data-color-scheme`-Attribut auf `<html>` gesetzt.

Eigene Schemas: In `main.css` neue `[data-color-scheme="..."]`-Blöcke ergänzen, die RGB-Tupel-Variablen definieren.

---

## Content-Management

Texte und Galerien können wahlweise als Markdown- bzw. JSON-Dateien im `content/`-Verzeichnis gepflegt werden:

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

In Templates stehen `$md('home/hero')` und `$gallery('home/gallery')` zur Verfügung.

---

## Environment Handling (DEV / PROD)

```env
APP_ENV=dev
# oder
APP_ENV=prod
```

- **dev**: Fehlerausgabe aktiviert
- **prod**: Fehlerausgabe deaktiviert, Logging aktiv

---

## Entwicklung

- Lokal getestet mit XAMPP
- Composer:
  ```bash
  composer install
  ```
- NPM (für JS-Tests):
  ```bash
  npm install
  ```
- Kein Build-Step für CSS oder JavaScript erforderlich

### Tests ausführen

```bash
composer test   # PHPUnit (PHP)
npm test        # Jest (JavaScript)
```

---

## Deployment

### Voraussetzungen
- Webserver mit PHP ≥ 8.0
- Apache mit aktiviertem `mod_rewrite`
- SMTP-Zugang für Mailversand
- HTTPS empfohlen

### Schritte
1. Projekt auf den Webserver hochladen
2. Abhängigkeiten installieren:
   ```bash
   composer install --no-dev
   ```
3. `.env` anlegen und konfigurieren (Vorlage: `.env.example`)
4. Schreibrechte für Sessions & temporäre Dateien sicherstellen
5. Formular-Flow testen (Kontakt / SEPA)

### .env Konfiguration

```env
APP_ENV=prod
FORM_TYPE=contact

MAIL_HOST=<smtp-server>
MAIL_PORT=<smtp-port>
MAIL_USER=<smtp-username>
MAIL_PASS=<smtp-password>
MAIL_FROM=<smtp-from-address>
MAIL_FROM_NAME=<smtp-from-name>
MAIL_TO=<recipient-email>
MAIL_SECURE=<tls|ssl>
```

### Hinweise
- PHP-Endpunkte sind über `.htaccess` abgesichert
- Keine Framework-Abhängigkeiten
- Klare Trennung von Layout, Routing, Formular- & Business-Logik

---

## Philosophy & Goals

- **Barrierefreiheit ist Teil der Architektur**
- **Einfachheit vor Komplexität**
- **Kein Framework-Zwang**
- **Security by default**
- **Nachvollziehbarer Code statt Overengineering**
- **Testbarkeit von Anfang an**

### Nicht-Ziele
- Kein CMS
- Kein Full-Framework
- Keine Garantie für vollständige WCAG-Konformität nach individuellen Anpassungen

---

## Lizenz
MIT License

---

## Autor & Unterstützung
- Jörg Römhild
- ChatGPT / Claude
