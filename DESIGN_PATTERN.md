# Design Patterns – One-Pager Template

Stand: v1.3.0
Ziel: Klare Trennung von Struktur, Logik und Darstellung
Prinzip: Weniger Magie, mehr Vorhersagbarkeit

---

## 1. Grundprinzipien

### Single Entry Point
- Alle Requests laufen über `public/index.php`
- Ausnahme: explizite Endpoints wie `send_kontakt.php`, `send_sepa.php`

### Whitelist-Routing
- Nur explizit definierte Routen sind erreichbar
- Keine dynamischen Template-Pfade
- 404 wird bewusst und kontrolliert gerendert

### Trennung der Verantwortlichkeiten
- **PHP**: Routing, Daten, Security
- **HTML**: Struktur
- **CSS**: Darstellung & Zustände
- **JS**: Interaktion & Zustandswechsel

### Template/Kunden-Trennung
- Template-Dateien (`src/`, `public/assets/js/`, `templates/`) und kundenspezifische Dateien (`content/`, `public/assets/images/`, `.env`) überschneiden sich nie
- Diese Trennung ist bewusst so designt, damit Kundenprojekte Template-Updates per `git merge` konfliktfrei übernehmen können
- Neue Features dürfen diese Grenze nicht verwischen — Kunden-Content bleibt in `content/`, Template-Logik bleibt in `src/` und `templates/`

---

## 2. Template- & Layout-Struktur

### base.php
- Enthält ausschließlich:
  - `<head>` (inkl. FOUC-Script synchron, kein defer)
  - Header
  - `<main>`
  - Cookie Notice
  - Footer
- Kein Inline-JavaScript
- Keine Business-Logik

### Pages
- Seiten (`home.php`, `impressum.php`, `datenschutz.php`)
- Enthalten **nur Content**
- Kein `<html>`, `<head>`, `<body>`

---

## 3. Security Headers

### Architektur
Security Headers werden in `src/Security/headers.php` als benannte Funktionen definiert:

- `setBaseSecurityHeaders()` — gemeinsame Header für alle Antworten
- `setApiSecurityHeaders()` — für JSON-Endpoints (`send_*.php`)
- `setHtmlSecurityHeaders()` — für HTML-Seiten inkl. CSP

### Regel
- Jeder Entry Point ruft die passende Funktion als **erste Aktion** nach `require autoload.php` auf
- Nie inline, nie implizit

---

## 4. FOUC Prevention & Color Scheme

### Problem
Beim Laden liest JS localStorage und setzt `data-theme` / `data-color-scheme` — aber erst nach CSS-Render → sichtbarer Flash.

### Lösung
`public/assets/js/fouc-prevention.js` wird **synchron im `<head>` geladen** (kein `defer`, kein `async`).
Es liest localStorage und setzt die Attribute auf `<html>`, bevor CSS gerendert wird.

### Color Scheme Architektur
- CSS definiert RGB-Tupel-Variablen pro Schema (`[data-color-scheme="..."]`)
- Semantische Tokens (`--color-bg-body` etc.) bleiben schema-agnostisch in `:root`
- Alle Komponenten-Styles verwenden ausschließlich semantische Tokens
- Neues Schema: neuen `[data-color-scheme="..."]`-Block in `main.css` ergänzen

### Regeln
- ❌ keine Hex-Farben direkt in Komponenten
- ❌ kein direkter Zugriff auf Primitive (`--color-primary` etc.) in Komponenten-CSS
- ✅ Primitive nur in Schema-Blöcken
- ✅ Komponenten nur auf semantische Tokens

---

## 5. Header & Navigation (UX)

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

## 6. Mobile Navigation

### Ziel
- Mobile Menü darf **nie in einem inkonsistenten Zustand bleiben**

### Schließbedingungen (verbindlich)
Das Menü **muss schließen**, wenn:
- ein Menü-Link angeklickt wird
- ein Anchor-Link (`#` oder `/#`) geklickt wird
- der Benutzer scrollt
- das Logo / Home-Link geklickt wird

### Umsetzung
- Zustand über `.is-open`
- ARIA-State (`aria-expanded`) wird synchron gepflegt
- Menü schließt **vor** Navigation

### Anti-Patterns
- ❌ doppelte Zustände (JS + CSS)
- ❌ Sonderlogik nur für das Logo
- ❌ Menü offen lassen bei Scroll

---

## 7. Footer

### Ziel
- Footer immer am unteren sichtbaren Rand
- Kein Überdecken von Content
- Immer bedienbar

### Umsetzung
- `position: fixed`
- Body / Layout berücksichtigt Footer-Höhe mit `padding-bottom`
- Color Scheme Selector im Footer: `<select data-color-scheme-select>`

---

## 8. Cookie Notice

### Zweck
DSGVO-Hinweis auf Session-Cookie für CSRF-Schutz.

### Verhalten
1. FOUC-Script prüft localStorage beim Laden — bereits dismisste Notice wird via `[data-cookie-dismissed="true"]` sofort ausgeblendet (kein Flash)
2. Nach Klick auf "Verstanden": CSS-Transition → `transitionend` → DOM-Entfernung
3. Wert bleibt dauerhaft in localStorage

### Regeln
- ❌ keine echte Cookie-Consent-Logik (das Template setzt nur technisch notwendige Cookies)
- ✅ Text anpassen wenn zusätzliche Cookies eingesetzt werden

---

## 9. Kontaktformular

### Architektur
- Formular-HTML: Template
- Validierung:
  - Client: UX
  - Server: verbindlich
- Versand: PHPMailer
- Konfiguration ausschließlich über `.env` (`MAIL_*`-Variablen)

### Security
- CSRF-Token (Pflicht)
- Honeypot-Feld
- Einheitliche JSON-Responses
- Keine internen Fehlerdetails im Frontend

### UX
- Erfolgsmeldung faded nach 5 Sekunden automatisch aus (`is-fading`-Klasse + `transitionend`)
- Fehlerfarbe bleibt stehen bis zur nächsten Aktion

---

## 10. UI-Komponenten

### Stats Counter
- HTML: `<span data-count-target="42">0</span>`
- JS: Count-Up-Animation via `requestAnimationFrame` + Intersection Observer
- `prefers-reduced-motion`: zeigt Endwert sofort, keine Animation

### Image Gallery + Lightbox
- HTML: `<ul id="galleryGrid">` mit `data-gallery-index`, `data-gallery-src`, `data-gallery-alt`
- Lightbox: `<div id="galleryLightbox">` mit `data-lightbox-close/prev/next`
- Keyboard: Pfeiltasten, Escape, Tab (Focus-Trap)
- Tabs: werden dynamisch aus `data-gallery-category` generiert — kein statisches Tab-HTML nötig
- `data-gallery-show-all="false"` unterdrückt den "Alle"-Tab

---

## 11. Content-Management

### Pattern
Markdown- und JSON-Dateien in `content/` werden über zwei Closure-Funktionen in `index.php` geladen:

- `$md('home/hero')` — lädt `content/home/hero.md` und rendert HTML
- `$gallery('home/gallery')` — lädt `content/home/gallery.json` und gibt Array zurück

### Sicherheit
- Pfadnamen werden vor dem Laden sanitiert (nur `a-z0-9/-_` erlaubt)
- Kein direkter Nutzerzugriff auf `content/`-Dateien

---

## 12. Section Flags

### Zweck
Sections der Startseite sind einzeln über `.env` de-/aktivierbar — ohne Code-Änderungen.

### Verfügbare Flags
| Flag | Section |
|------|---------|
| `SECTION_HERO` | Hero-Bereich |
| `SECTION_GALLERY` | Galerie + Lightbox |
| `SECTION_STATS` | Stats Counter |
| `SECTION_ABOUT` | About + Cards |
| `SECTION_CONTACT` | Kontakt-/SEPA-Formular |

### Mechanismus
- `$section()`-Closure in `index.php` liest `SECTION_*` aus `$_ENV`
- Default: `true` — fehlt ein Flag, bleibt die Section aktiv (keine Breaking Change)
- Deaktivieren: Flag auf `false` setzen (`SECTION_STATS=false`)

### Regeln
- ❌ keine Section-Logik im Template selbst — nur `<?php if ($section('...')): ?>`
- ❌ kein JS oder CSS für deaktivierte Sections — das Rendering entfällt vollständig
- ✅ Lightbox immer zusammen mit `SECTION_GALLERY` ein-/ausgeblendet (logische Einheit)

---

## 13. JavaScript-Philosophie

- IIFE pro Feature (A, B, B2, C, C1–C4, D, E, F, G, H)
- Kein globaler State
- Defensive Selektoren (`if (!el) return`)
- Eine Verantwortung pro Block
- Alle Feature-Module sind optional (fehlende DOM-Elemente lösen kein Error aus)

---

## 13. Testing

### PHP (PHPUnit 10)
- Testklassen in `tests/` (Namespace `Tests\`)
- Abgedeckt: CSRF, Escaper, Helpers, IbanValidator
- Ausführen: `composer test`

### JavaScript (Jest 29)
- Tests in `tests/js/`
- Abgedeckt: alle IIFE-Module (A, B, B2, C, D, E, F, G, H) + FOUC-Prevention
- Ausführen: `npm test`

### Philosophie
- Neue JS-Module: zugehörigen Test anlegen
- Neue PHP-Klassen/Funktionen: zugehörigen PHPUnit-Test anlegen
- Tests laufen ohne Netzwerk, ohne Datenbank, ohne Dateiystem-Abhängigkeiten

---

## 14. Was bewusst **nicht** gemacht wird

- Kein Framework
- Kein State-Management
- Keine Magie im Autoload
- Keine impliziten Abhängigkeiten

---

## Status
✔ UX stabil
✔ Security-Baseline aktiv
✔ Color Scheme System aktiv
✔ Testing-Infrastruktur eingerichtet
✔ Release-tauglich (v1.3.0)
