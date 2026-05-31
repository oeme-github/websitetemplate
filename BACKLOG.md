# Backlog – One-Pager Website Template

## Letzter Stand

**Version:** v1.4.0 (gepusht)  
**Zuletzt abgeschlossen:** Paket A + B + Mailpit-Setup + Integration-Tests

### Abgeschlossen in dieser Session

**Paket A — Formular-Bootstrap vereinheitlicht (Issues #2, #3, #6)**
- `src/http/FormEndpoint.php`: `respond()`, `formBootstrap()`, `guardMethod()`, `guardCsrf()`, `guardHoneypot()`, `requireEnvKeys()`
- Bootstrap-Duplikat aus beiden Endpoints entfernt
- `debug`-Feld nur noch bei `APP_ENV=dev`
- SEPA: Whitelist-Validierung für `betrag`, `zahlungsrhythmus`, `mitgliedschaft`

**Paket B — Feldbezogene Fehleranzeige (Issues #4, #9 teilweise)**
- Backend: `errors` als `{field: message}`-Map statt flat Array
- Frontend: `clearFieldErrors()`, `showFieldErrors()` mit `aria-invalid` + `aria-describedby`
- Scroll-to + Fokus auf erstes Fehlerfeld
- CSS: `.field-error-msg` für inline Fehlertexte
- JS-Tests: +3 Tests (field-level behavior)

**Mailpit-Setup**
- `.env` für Dev angelegt (Mailpit auf `127.0.0.1:1025`)
- PHPMailer: `SMTPAutoTLS` und `SMTPAuth` env-gesteuert (kein Hardcode mehr)
- `MAIL_SECURE`, `MAIL_USER`, `MAIL_PASS` optional (nicht in `requireEnvKeys`)
- `.env.example` + `CLAUDE.md` mit Mailpit-Anleitung aktualisiert
- Stale hero-Preload aus `base.php` entfernt

**Integration-Tests**
- `tests/Integration/KontaktFormTest.php`: 3 E2E-Tests via HTTP → Apache → Mailpit
- `composer test` (Unit, 48 Tests) vs. `composer test-integration` (E2E, 3 Tests) sauber getrennt

---

## Offene Issues (GitHub)

| # | Titel | Priorität | Nächste Session |
|---|-------|-----------|-----------------|
| #4 | Formular-UX verfeinern | Mittel | Paket B Rest / Paket C |
| #9 | Accessibility Feinschliff | Mittel | Paket B Rest |
| #7 | Rate Limiting für Formulare | Mittel | Paket C |
| #8 | IBAN UX-Verbesserung (Frontend) | Mittel | Paket C |
| #1 | SEPA-Flow rechtlich finalisieren | Offen | Wartet auf User-Input |
| #5 | Logging-Strategie (DEV vs PROD) | Offen | Wartet auf User-Input |

---

## Nächste Session: Paket C

**Ziel:** Rate Limiting + IBAN Live-Validierung im Frontend

### Aufgaben
- [ ] Rate Limiting (Session-basiert) für Formular-Endpoints (Issue #7)
- [ ] IBAN Live-Validierung im Frontend: Eingabe prüfen, Bankname per API nachladen (Issue #8)
- [ ] SEPA Integration-Test analog zu KontaktFormTest
- [ ] Issues #4, #9 reviewen ob noch offen

---

## Zurückgestellt

- **Issue #1** (SEPA rechtlich): Wartet auf Juristencheck / Pflichttexte
- **Issue #5** (Logging): Wartet auf Entscheidung zu Request-ID
