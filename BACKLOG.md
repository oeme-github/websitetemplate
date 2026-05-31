# Backlog – One-Pager Website Template

## Letzter Stand

**Version:** v1.4.0 (in Arbeit)  
**Zuletzt abgeschlossen:** Paket A — Formular-Bootstrap vereinheitlicht + Backend-Validierung vervollständigt

### Abgeschlossen in der letzten Session (Paket A)
- `src/http/FormEndpoint.php` neu: `respond()`, `formBootstrap()`, `guardMethod()`, `guardCsrf()`, `guardHoneypot()`, `requireEnvKeys()`
- Bootstrap-Duplikat aus beiden Endpoints entfernt (Issue #2 ✅)
- `send_kontakt.php` auf gemeinsame Bootstrap-Logik umgestellt
- `send_sepa.php` auf gemeinsame Bootstrap-Logik umgestellt
- `debug`-Feld im Exception-Handler nur noch in `APP_ENV=dev`
- `?? ''` für alle `$_POST`-Zugriffe ergänzt (keine PHP-Warnings mehr)
- SEPA: Whitelist-Validierung für `betrag`, `zahlungsrhythmus`, `mitgliedschaft` (Issue #3 ✅)
- SEPA: `$plz == ''` → `=== ''` korrigiert
- Issue #6 auf GitHub geschlossen ✅
- Alle Tests grün: PHPUnit 48, Jest 86

---

## Offene Issues (GitHub)

| # | Titel | Priorität | Nächste Session |
|---|-------|-----------|-----------------|
| #4 | Formular-UX verfeinern | Mittel | Paket B |
| #9 | Accessibility Feinschliff | Mittel | Paket B |
| #7 | Rate Limiting für Formulare | Mittel | Paket C |
| #8 | IBAN UX-Verbesserung (Frontend) | Mittel | Paket C |
| #1 | SEPA-Flow rechtlich finalisieren | Offen | Wartet auf User-Input |
| #5 | Logging-Strategie (DEV vs PROD) | Offen | Wartet auf User-Input |

---

## Nächste Session: Paket B

**Ziel:** Feldbezogene Fehleranzeige + Accessibility-Feinschliff

### Aufgaben
- [ ] Server-Fehler auf Felder mappen (JSON `errors` Array → Feldname als Key)
- [ ] Frontend: Fehler per `aria-describedby` mit dem zugehörigen Feld verknüpfen
- [ ] Scroll-to-first-error bei Validierungsfehler
- [ ] Fokus-Management: nach Fehler → erstes Fehlerfeld, nach Erfolg → Erfolgsmeldung
- [ ] Screenreader-Test SEPA-Form (Issue #9)
- [ ] Issues #2, #3 auf GitHub schließen

---

## Danach: Paket C

- Rate Limiting (Session-basiert, Issue #7)
- IBAN Live-Validierung im Frontend (Issue #8)

## Zurückgestellt

- **Issue #1** (SEPA rechtlich): Wartet auf Juristencheck / Pflichttexte
- **Issue #5** (Logging): Wartet auf Entscheidung zu Request-ID
