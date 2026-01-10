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
- Formular-System:
  - Kontaktformular **oder**
  - alternatives **SEPA-Formular**
- Umschaltbares Formular über `.env`
- CSRF- & Honeypot-Schutz
- AJAX-Submit mit ARIA-Feedback
- Legal-Pages für Impressum und Datenschutz

---

## Technik
- HTML5 / CSS3
- Vanilla JavaScript
- PHP ≥ 8.0
- Composer (PHPMailer, TCPDF)
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

## Environment Handling (DEV / PROD)

Das Verhalten von PHP-Fehlerausgaben wird über `APP_ENV` gesteuert:

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
- Kein Build-Step für CSS oder JavaScript erforderlich

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
   composer install
   ```
3. `.env` anlegen und konfigurieren
4. Schreibrechte für Sessions & temporäre Dateien sicherstellen
5. Formular-Flow testen (Kontakt / SEPA)

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
- ChatGPT
