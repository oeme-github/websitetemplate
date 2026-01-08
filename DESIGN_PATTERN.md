# Design Patterns – One-Pager Template

Stand: UX- & Security-Zwischenstand
Ziel: Klare Trennung von Struktur, Logik und Darstellung
Prinzip: Weniger Magie, mehr Vorhersagbarkeit

---

## 1. Grundprinzipien

### Single Entry Point
- Alle Requests laufen über `public/index.php`
- Ausnahme: explizite Endpoints wie `send_kontakt.php`

### Whitelist-Routing
- Nur explizit definierte Routen sind erreichbar
- Keine dynamischen Template-Pfade
- 404 wird bewusst und kontrolliert gerendert

### Trennung der Verantwortlichkeiten
- **PHP**: Routing, Daten, Security
- **HTML**: Struktur
- **CSS**: Darstellung & Zustände
- **JS**: Interaktion & Zustandswechsel

---

## 2. Template- & Layout-Struktur

### base.php
- Enthält ausschließlich:
  - `<head>`
  - Header
  - `<main>`
  - Footer
- Kein Inline-JavaScript
- Keine Business-Logik

### Pages
- Seiten (`home.php`, `impressum.php`, `datenschutz.php`)
- Enthalten **nur Content**
- Kein `<html>`, `<head>`, `<body>`

---

## 3. Header & Navigation (UX)

### Ziel
- Header inkl. Topbar:
  - verschwindet beim Scrollen nach unten
  - erscheint beim Scrollen nach oben
- Verhalten muss konsistent sein bei:
  - Maus
  - Touch
  - Anchor-Navigation

### Umsetzung
- Header ist in `.site-header` gekapselt
- Sichtbarkeit wird über `.is-hidden` gesteuert
- JavaScript wertet `window.scrollY` + Scrollrichtung aus
- CSS übernimmt ausschließlich die Darstellung

### Regeln
- ❌ kein `position: fixed` für Header
- ❌ kein Scroll-Container (`overflow: auto`)
- ❌ keine Inline-JS-Logik
- ✅ CSS = Zustand, JS = Entscheidung

---

## 4. Mobile Navigation

### Ziel
- Mobile Menü darf **nie in einem inkonsistenten Zustand bleiben**

### Schließbedingungen (verbindlich)
Das Menü **muss schließen**, wenn:
- ein Menü-Link angeklickt wird
- ein Anchor-Link (`#` oder `/#`) geklickt wird
- der Benutzer scrollt
- das Logo / Home-Link geklickt wird
- Edge-Case:
  - Seite neu laden
  - Menü öffnen
  - Home klicken

### Umsetzung
- Zustand über `.is-open`
- ARIA-State (`aria-expanded`) wird synchron gepflegt
- Menü schließt **vor** Navigation
- Keine Abhängigkeit von `hashchange`

### Anti-Patterns
- ❌ doppelte Zustände (JS + CSS)
- ❌ Sonderlogik nur für das Logo
- ❌ Menü offen lassen bei Scroll

---

## 5. Footer

### Ziel
- Footer immer am unteren sichtbaren Rand
- Kein Überdecken von Content
- Immer bedienbar

### Umsetzung
- `position: fixed`
- Body / Layout berücksichtigt Footer-Höhe
- Keine Scroll-Logik im Footer

---

## 6. Kontaktformular

### Architektur
- Formular-HTML: Template
- Validierung:
  - Client: UX
  - Server: verbindlich
- Versand: PHPMailer
- Konfiguration ausschließlich über `.env`

### Security
- CSRF-Token (Pflicht)
- Honeypot-Feld
- Einheitliche JSON-Responses
- Keine internen Fehlerdetails im Frontend

---

## 7. JavaScript-Philosophie

- IIFE pro Feature
- Kein globaler State
- Defensive Selektoren (`if (!el) return`)
- Eine Verantwortung pro Block

---

## 8. Was bewusst **nicht** gemacht wird

- Kein Framework
- Kein State-Management
- Keine Magie im Autoload
- Keine impliziten Abhängigkeiten

---

## Status
✔ UX stabil
✔ Security-Baseline aktiv
✔ Release-tauglich
