# Review Checklist – One-Pager Template

Ziel: Reproduzierbarer Qualitätsstandard
Status: Finaler UX- & Security-Smoke-Test

---

## 1. Routing & Architektur

- [x] Single Entry Point (`index.php`)
- [x] Whitelist-Routing
- [x] Saubere 404-Seite
- [x] Keine direkten Template-Zugriffe
- [x] Rewrite-Regeln greifen korrekt

---

## 2. Security-Baseline

- [x] X-Powered-By entfernt
- [x] Security-Header gesetzt
- [x] CSP aktiv
- [x] CSRF-Schutz aktiv
- [x] Honeypot aktiv
- [x] Keine internen Fehlermeldungen im Frontend

---

## 3. Header / Topbar (UX)

- [x] Initial sichtbar
- [x] Verschwindet bei Scroll nach unten
- [x] Erscheint bei Scroll nach oben
- [x] Kein Flackern
- [x] Korrektes Verhalten bei Anchor-Navigation

---

## 4. Mobile Navigation

- [x] Öffnen / Schließen per Toggle
- [x] Schließt bei Klick auf Menü-Link
- [x] Schließt bei Klick auf Logo / Home
- [x] Schließt bei Scroll
- [x] Edge-Case:
  - Reload → Menü öffnen → Home klicken
- [x] `aria-expanded` immer korrekt
- [x] Kein Fokus- oder Scroll-Bug

---

## 5. Footer

- [x] Immer sichtbar (fixed)
- [x] Kein Content verdeckt
- [x] Letzte Section erreichbar
- [x] Links & Theme-Toggle funktionieren

---

## 6. Kontaktformular

- [x] HTML-Validierung vorhanden
- [x] Server-Validierung vorhanden
- [x] CSRF wird geprüft
- [x] Honeypot greift
- [x] Einheitliche JSON-Antworten
- [x] Fehler korrekt im Frontend dargestellt
- [x] Umlaute korrekt (UTF-8 End-to-End)

---

## 7. JavaScript

- [x] main.js nur einmal eingebunden
- [x] Keine Inline-Skripte
- [x] Keine globalen Variablen
- [x] Defensive Selektoren
- [x] Fehlerbehandlung vollständig

---

## 8. UX-Fazit

- [x] Erwartbares Verhalten
- [x] Keine bekannten Edge-Cases offen
- [x] Mobile & Desktop konsistent
- [x] Release-fähig

---

## Abschluss
✔ Zwischenstand freigegeben
✔ Dokumentation aktuell
✔ Basis für Erweiterungen stabil
