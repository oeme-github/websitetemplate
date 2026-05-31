# Review Checklist – One-Pager Template

Ziel: Reproduzierbarer Qualitätsstandard
Stand: v1.3.0

---

## 1. Routing & Architektur

- [x] Single Entry Point (`index.php`)
- [x] Whitelist-Routing
- [x] Saubere 404-Seite
- [x] Keine direkten Template-Zugriffe
- [x] Rewrite-Regeln greifen korrekt

---

## 2. Security-Baseline

- [x] `X-Powered-By` entfernt (`setBaseSecurityHeaders()`)
- [x] Security-Header gesetzt (alle Entry Points rufen Header-Funktion auf)
- [x] CSP aktiv
- [x] CSRF-Schutz aktiv
- [x] Honeypot aktiv
- [x] Keine internen Fehlermeldungen im Frontend

---

## 3. FOUC Prevention & Themes

- [x] `fouc-prevention.js` synchron im `<head>` (kein defer)
- [x] Kein Theme-Flash beim Reload
- [x] Kein Color-Scheme-Flash beim Reload
- [x] Cookie-Notice erscheint nicht nochmal nach Dismiss + Reload

---

## 4. Color Scheme Selector

- [x] Selector im Footer sichtbar und bedienbar
- [x] Farbwechsel sofort sichtbar
- [x] Schema bleibt nach Reload erhalten
- [x] Ungültige localStorage-Werte fallen auf Default zurück

---

## 5. Cookie Notice

- [x] Erscheint beim ersten Besuch
- [x] Dismiss-Button schließt die Notice (Transition + DOM-Entfernung)
- [x] Erscheint nach Reload **nicht** mehr
- [x] Link zur Datenschutzerklärung funktioniert

---

## 6. Header / Topbar (UX)

- [x] Initial sichtbar
- [x] Verschwindet bei Scroll nach unten
- [x] Erscheint bei Scroll nach oben
- [x] Kein Flackern
- [x] Korrektes Verhalten bei Anchor-Navigation

---

## 7. Mobile Navigation

- [x] Öffnen / Schließen per Toggle
- [x] Schließt bei Klick auf Menü-Link
- [x] Schließt bei Klick auf Logo / Home
- [x] Schließt bei Scroll
- [x] Edge-Case: Reload → Menü öffnen → Home klicken
- [x] `aria-expanded` immer korrekt
- [x] Kein Fokus- oder Scroll-Bug

---

## 8. Footer

- [x] Immer sichtbar (fixed)
- [x] Kein Content verdeckt
- [x] Letzte Section erreichbar
- [x] Links, Theme-Toggle & Color Scheme Selector funktionieren

---

## 9. Stats Counter

- [x] Count-Up startet beim Scrollen in die Section
- [x] Endwert wird korrekt angezeigt
- [x] `prefers-reduced-motion`: Endwert sofort, keine Animation

---

## 10. Gallery & Lightbox

- [x] Bilder öffnen in Lightbox
- [x] Vorwärts/Rückwärts-Navigation funktioniert
- [x] Keyboard: Pfeiltasten und Escape funktionieren
- [x] Focus-Trap in Lightbox aktiv
- [x] Fokus kehrt nach Schließen zum Auslöser zurück
- [x] Gallery-Tabs filtern korrekt (wenn `data-gallery-category` gesetzt)
- [x] `aria-pressed` auf Tabs korrekt gesetzt

---

## 11. Kontaktformular

- [x] HTML-Validierung vorhanden
- [x] Server-Validierung vorhanden
- [x] CSRF wird geprüft
- [x] Honeypot greift
- [x] Einheitliche JSON-Antworten
- [x] Fehler korrekt im Frontend dargestellt
- [x] Erfolgsmeldung faded nach 5 Sekunden aus
- [x] Umlaute korrekt (UTF-8 End-to-End)

---

## 12. JavaScript

- [x] `main.js` nur einmal eingebunden (defer)
- [x] `fouc-prevention.js` synchron im Head (kein defer)
- [x] Keine Inline-Skripte
- [x] Keine globalen Variablen
- [x] Defensive Selektoren (fehlende Elemente lösen keinen Error aus)
- [x] Fehlerbehandlung vollständig

---

## 13. Testing

- [x] `composer test` läuft fehlerfrei (PHPUnit)
- [x] `npm test` läuft fehlerfrei (Jest)
- [x] Neue Features sind durch Tests abgedeckt

---

## 14. UX-Fazit

- [x] Erwartbares Verhalten
- [x] Keine bekannten Edge-Cases offen
- [x] Mobile & Desktop konsistent
- [x] Release-fähig

---

## Abschluss
✔ v1.3.0 freigegeben
✔ Dokumentation aktuell
✔ Basis für Erweiterungen stabil
