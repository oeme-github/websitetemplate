# REVIEW_CHECKLIST.md

Diese Checkliste dient zur schnellen Prüfung von Änderungen
gegen die definierten Design- und Security-Patterns.

---

## 1. Projektstruktur

- [ ] Webroot zeigt nur auf `public/`
- [ ] Kein direkter Zugriff auf `src/`, `templates/`, `vendor/`
- [ ] Keine PHP-Dateien im Projekt-Root

---

## 2. Templates & Rendering

- [ ] Layout enthält HTML-Gerüst und `<main>`
- [ ] Seiten-Templates enthalten nur Inhalt
- [ ] Keine Security- oder Business-Logik in Templates
- [ ] Partials sind wiederverwendbar

---

## 3. Formular & Security

- [ ] Enthält jedes POST-Formular ein CSRF-Token?
- [ ] Wird CSRF vor Zugriff auf `$_POST` geprüft?
- [ ] Ist ein Honeypot-Feld vorhanden?
- [ ] Wird ein Honeypot-Treffer still verarbeitet?
- [ ] Gibt es genau einen Endpunkt pro Formular?
- [ ] Erfolgt Validierung serverseitig?
- [ ] Werden keine User-Daten ungefiltert ausgegeben?

---

## 4. Mail & Konfiguration

- [ ] Wird PHPMailer statt `mail()` verwendet?
- [ ] Sind SMTP-Daten ausgelagert (`.env`)?
- [ ] Ist `.env` in `.gitignore`?
- [ ] Existiert eine `.env.example`?
- [ ] Wird `Reply-To` statt `From` verwendet?
- [ ] Gibt es keine Secrets im Repository?

---

## 5. Security-Header & Sessions

- [ ] Security-Header sind gesetzt (CSP, XFO, XCTO, Referrer-Policy)
- [ ] Sessions werden zentral initialisiert
- [ ] Keine Security-Logik im Template

---

## 6. Abschluss-Check

- [ ] Würde ein neuer Entwickler die Struktur verstehen?
- [ ] Ist die Änderung sicherer oder gleich sicher?
- [ ] Würde die Änderung auch in 6 Monaten noch Sinn ergeben?
