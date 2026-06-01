# Backlog – One-Pager Website Template

## Letzter Stand

**Version:** v1.5.0 + History-Bereinigung (gepusht)  
**Zuletzt abgeschlossen:** B-WG-Bilder aus gesamter Git-History entfernt

### Abgeschlossen in dieser Session

**Git-History-Bereinigung**
- 12 projektspezifische B-WG-Bilder via `git filter-repo --force --invert-paths` aus **gesamter History** entfernt
- 11 graue 800×600 Placeholder-Bilder (ImageMagick) eingecheckt — `.xcf` ohne Ersatz
- GitHub force-gepusht, alle Commit-SHAs neu geschrieben
- Windows-Kopie via `git reset --hard origin/main` synchronisiert
- ⚠️ Hinweis: echte Bilder für `beatmungswg-ofterdingen` lokal aufbewahren, **nicht** in Git einchecken

**Issues geschlossen**
- Issue #4 (Formular-UX): alle Punkte durch Paket B/C abgedeckt — geschlossen
- Issue #9 (Accessibility): `aria-describedby` + Fokus-Management durch Paket B abgedeckt — geschlossen; Screenreader-Test ins Backlog

**Testzahlen gesamt:**
- PHP Unit: 55 Tests
- JS Jest: 101 Tests
- Integration: 7 Tests

---

## Offene Issues (GitHub)

| # | Titel | Priorität | Nächste Session |
|---|-------|-----------|-----------------|
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
