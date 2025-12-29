# Contributing

Vielen Dank für dein Interesse an diesem Projekt!
Beiträge sind willkommen – bitte beachte die folgenden Leitlinien.

---

## Grundsätze

- **Klarheit vor Cleverness**
  Änderungen sollten gut lesbar und wartbar bleiben.
- **Barrierefreiheit ist Teil des Konzepts**
  Neue Features dürfen die bestehende Accessibility nicht verschlechtern.
- **Keine Framework-Abhängigkeiten**
  Das Projekt bleibt bewusst frameworkfrei (Vanilla JS / PHP).

---

## Arten von Beiträgen

Willkommen sind insbesondere:
- Bugfixes
- Verbesserungen der Barrierefreiheit
- Code-Qualitätsverbesserungen
- Dokumentations-Verbesserungen
- kleine, klar abgegrenzte Features

Bitte vorab abstimmen bei:
- größeren strukturellen Änderungen
- neuen Abhängigkeiten
- umfangreichen Feature-Erweiterungen

---

## Code-Richtlinien

### HTML / CSS
- Semantisches HTML bevorzugen
- Keine unnötigen Wrapper
- CSS möglichst über bestehende Design-Tokens (`:root`) steuern

### JavaScript
- Vanilla JavaScript (kein Framework)
- Klare Funktionsnamen
- Kommentare nur dort, wo das *Warum* erklärt werden muss

### PHP
- PHP ≥ 8
- Strikte Typisierung (`declare(strict_types=1);`)
- Serverseitige Validierung ist Pflicht
- Sicherheitsrelevante Änderungen bitte begründen

---

## Pull Requests

1. Fork erstellen
2. Feature-Branch anlegen
3. Änderungen implementieren
4. Pull Request mit kurzer Beschreibung eröffnen

Bitte beschreibe im PR:
- **Was** geändert wurde
- **Warum** die Änderung sinnvoll ist
- ob es Auswirkungen auf Accessibility oder Security gibt

---

## Code of Conduct

Sei respektvoll und konstruktiv.
Dieses Projekt soll ein freundlicher Ort für Austausch und Lernen sein.
