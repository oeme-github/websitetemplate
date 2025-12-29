# One-Pager Website

Diese One-Page-Website ist als **Template** gedacht.
Sie bietet einen soliden Ausgangspunkt für eine einfache, barrierearme Website mit mehreren Sektionen und einem Kontaktformular.

Das Template erhebt **keinen Anspruch auf Vollständigkeit**, legt jedoch Wert auf:
- saubere Struktur
- gute Wartbarkeit
- Barrierefreiheit (WCAG-orientiert)

⚠️ Hinweis:
Nach eigenen Anpassungen (z. B. Farben, Schriften, Inhalte) sollte die Barrierefreiheit erneut geprüft werden.

---

## Features
- Responsive One-Pager
- Barrierefreies Mobile-Menü
- Kontaktformular mit CSRF- & Honeypot-Schutz
- AJAX-Submit mit ARIA-Feedback

---

## Technik
- HTML5 / CSS3
- Vanilla JavaScript
- PHP 8 (PHPMailer)
  - Konfiguration über `/PHP/.env`
- Lighthouse:
  - Performance: 98
  - Accessibility: 100
  - Best Practices: 100
  - SEO: 100

---

## Entwicklung
- Lokal getestet mit XAMPP
- Composer ('composer install' im PHP-Verzeichnis ausführen)

---

## Customizing
- Zentrale Design-Tokens über `:root`
- Theme-Wechsel über `data-theme="dark"`

---

## Deployment

### Voraussetzungen
- Webserver mit PHP ≥ 8.0
- Aktiviertes `mod_rewrite` (Apache)
- Composer [optional, wenn PHP-Module mit deployed werden]
- SMTP-Zugang für den Mailversand

### Schritte
1. Projekt auf den Webserver hochladen
2. Abhängigkeiten installieren:
    cd /PHP
    composer install
2. `.env`-Datei in `/PHP/` anlegen und konfigurieren
3. Schreibrechte für Session-Handling sicherstellen
4. HTTPS aktivieren (empfohlen)
5. Formular testen (Mailversand & Validierung)

### Hinweise
- Die PHP-Endpunkte sind über `.htaccess` abgesichert
- JavaScript und CSS benötigen keine Build-Schritte

---

## Lizenz
MIT License

---

## Autor & Unterstützung
- Jörg Römhild
- ChatGPT
