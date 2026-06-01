# Backlog – One-Pager Website Template

## Letzter Stand

**Version:** v1.5.0 (gepusht)  
**Zuletzt abgeschlossen:** Paket C — Rate Limiting + IBAN Live-Validierung + SEPA Integration-Tests

### Abgeschlossen in dieser Session

**Paket C — Rate Limiting (Issue #7)**
- `src/http/FormEndpoint.php`: `rateLimitCheck()` (pure, testbar) + `guardRateLimit()`
- `guardRateLimit('kontakt')` / `guardRateLimit('sepa')` in beiden Endpoints (nach `guardHoneypot()`)
- Default: 5 Versuche / 15 Minuten, HTTP 429 bei Überschreitung
- `ibanLookupAllowed()` aus `send_sepa.php` entfernt → `rateLimitCheck()` direkt
- `tests/RateLimitTest.php`: 7 Tests

**Paket C — IBAN Live-Validierung (Issue #8)**
- `public/iban_lookup.php`: neuer GET-Endpoint, session-rate-limited (10/h)
- `public/.htaccess`: `iban_lookup.php` in Allow-Liste
- `sepa.php`: `id="iban"`, `id="bank"` ergänzt, `name="Bank"` → `name="bank"` gefixt, Status-Span
- `main.css`: `.iban-status` mit `is-loading` / `is-valid` / `is-invalid`
- `main.js`: IIFE „I" — debounced Input (600ms), Format-Check, Fetch, Bankfeld + Status
- `tests/js/ibanLookup.test.js`: 12 Tests

**SEPA Integration-Tests**
- `tests/Integration/SepaFormTest.php`: 4 E2E-Tests via HTTP → Apache → Mailpit
  - Mail mit PDF-Anhang (SEPA-Mandat.pdf)
  - Validierungsfehler → keine Mail
  - Ungültige IBAN → Feldfehler
  - CSRF-Rotation nach Erfolg
- `composer test-integration` jetzt: 7 Tests (3 Kontakt + 4 SEPA)

**Testzahlen gesamt:**
- PHP Unit: 55 Tests
- JS Jest: 101 Tests
- Integration: 7 Tests

---

## Offene Issues (GitHub)

| # | Titel | Priorität | Nächste Session |
|---|-------|-----------|-----------------|
| #4 | Formular-UX verfeinern | Mittel | Nächste Session reviewen |
| #9 | Accessibility Feinschliff | Mittel | Nächste Session reviewen |
| #1 | SEPA-Flow rechtlich finalisieren | Offen | Wartet auf User-Input |
| #5 | Logging-Strategie (DEV vs PROD) | Offen | Wartet auf User-Input |

---

## Nächste Session

- **Screenreader-Test SEPA-Formular** (aus Issue #9): manueller Test mit NVDA oder VoiceOver — SEPA-Formular durchspielen, Fehlermeldungen + Fokus-Verhalten prüfen
- Issue #1 (SEPA rechtlich) — wartet auf User-Input
- Issue #5 (Logging) — wartet auf User-Input

---

## Zurückgestellt

- **Issue #1** (SEPA rechtlich): Wartet auf Juristencheck / Pflichttexte
- **Issue #5** (Logging): Wartet auf Entscheidung zu Request-ID
