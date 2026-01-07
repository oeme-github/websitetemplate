# DESIGN_PATTERN.md

## Ziel
Dieses Dokument definiert verbindliche Architektur-, Security- und Strukturregeln
für das Projekt. Es dient als Leitplanke für Implementierung, Reviews und Erweiterungen.

---

## 1. Grundarchitektur

- Single Entry Point über `public/index.php`
- Webroot zeigt ausschließlich auf `public/`
- Nicht-öffentlicher Code liegt in:
  - `src/`
  - `templates/`
  - `vendor/`
- URLs sind stabil, Dateinamen sind intern

---

## 2. Templates & Rendering

- Layout (`templates/layout/base.php`) enthält:
  - HTML-Grundgerüst
  - `<main>`-Element
  - Einbindung von Header & Footer
- Seiten-Templates (`templates/pages/*`):
  - enthalten ausschließlich Inhalt
  - kein `<html>`, `<body>`, `<main>`, `<header>`, `<footer>`
- Partials (`templates/partials/*`) sind wiederverwendbar und logikfrei

---

## 3. Security – Grundsatz

Security wird ausschließlich **serverseitig** umgesetzt.
Frontend (HTML/JS) dient nur der Usability, nicht der Durchsetzung von Security.

---

## 4. CSRF-Schutz

- Jedes POST-Formular muss ein CSRF-Token enthalten
- CSRF-Tokens sind:
  - sessionbasiert
  - kryptografisch zufällig
  - serverseitig validiert
- CSRF-Prüfung erfolgt **vor**:
  - Zugriff auf `$_POST`
  - Validierung
  - Verarbeitung

Regel:
> Kein Zugriff auf Nutzerdaten, bevor CSRF erfolgreich geprüft wurde.

---

## 5. Honeypot (Bot-Schutz)

- Jedes Formular enthält ein Honeypot-Feld
- Wird das Feld ausgefüllt:
  - Request wird still verworfen
  - HTTP 200 wird zurückgegeben
  - keine Fehlermeldung, kein Feedback

---

## 6. Formular-Verarbeitung

- Jedes Formular hat genau **einen** Endpunkt
- Reihenfolge der Verarbeitung:
  1. Request-Methode prüfen (POST)
  2. CSRF prüfen
  3. Honeypot prüfen
  4. Eingaben lesen
  5. Validieren
  6. Verarbeiten (z. B. Mail)
  7. CSRF-Token regenerieren

- Fehlerantworten:
  - sind generisch
  - enthalten keine internen Details
  - enthalten keine Feldnamen

---

## 7. E-Mail-Versand

- E-Mail-Versand erfolgt ausschließlich über **PHPMailer**
- SMTP-Zugangsdaten sind ausgelagert (`.env`)
- `From` ist immer eine feste Systemadresse
- Benutzer-E-Mail wird ausschließlich als `Reply-To` gesetzt

Verboten:
- `mail()`
- dynamisches Setzen von `From` aus User-Input

---

## 8. Konfiguration & Secrets (.env)

- Sensible Konfiguration liegt in einer `.env`-Datei im Projekt-Root
- `.env` wird niemals committed
- `.env.example` gehört ins Repository
- Zugriff auf Konfigurationswerte erfolgt über Helperfunktion `$_ENV`

Regel:
> Keine Secrets im Code, keine Secrets im Frontend.

---

Siehe SECURITY_APPENDIX.md für Threat Model & Gegenmaßnahmen.

---