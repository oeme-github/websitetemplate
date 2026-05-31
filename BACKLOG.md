# Backlog – One-Pager Website Template

## Letzter Stand

**Version:** v1.3.0 (gepusht)  
**Zuletzt abgeschlossen:** Backport aus friendsofthehawks + vollständige Dokumentation

### Abgeschlossen in der letzten Session
- Security Headers refaktoriert (3 benannte Funktionen)
- FOUC Prevention + RGB Color Scheme System (Default / Warm / Nature)
- Cookie Notice (PHP, CSS, JS)
- Stats Counter + Image Gallery + Lightbox
- Form Erfolgsmeldung Auto-Fade (5 s)
- Content-Management (Parsedown + content/ Struktur)
- Testing-Infrastruktur: PHPUnit 48 Tests + Jest 86 Tests
- CLAUDE.md + BACKLOG.md angelegt
- Docs aktualisiert (README, DESIGN_PATTERN, REVIEW_CHECKLIST, SECURITY_APPENDIX)

---

## Offene Issues (GitHub)

| # | Titel | Priorität | Nächste Session |
|---|-------|-----------|-----------------|
| #2 | Formular-Endpoints vereinheitlichen | Hoch | ✅ Paket A |
| #3 | Backend-Validierung vervollständigen | Hoch | ✅ Paket A |
| #4 | Formular-UX verfeinern | Mittel | Paket B |
| #7 | Rate Limiting für Formulare | Mittel | Paket C |
| #8 | IBAN UX-Verbesserung (Frontend) | Mittel | Paket C |
| #9 | Accessibility Feinschliff | Mittel | Paket B |
| #1 | SEPA-Flow rechtlich finalisieren | Offen | Wartet auf User-Input |
| #5 | Logging-Strategie (DEV vs PROD) | Offen | Wartet auf User-Input |
| #6 | Dokumentation erweitern | ✅ Erledigt | Schließen |

---

## Nächste Session: Paket A

**Ziel:** Gemeinsame Bootstrap-Logik für Formular-Endpunkte + vollständige Backend-Validierung

### Aufgaben
- [ ] `respond()` + Error-Handling aus `send_kontakt.php` / `send_sepa.php` in eine gemeinsame Datei extrahieren (z. B. `src/http/FormEndpoint.php` oder `src/Helpers/respond.php`)
- [ ] Einheitliche Fehlercodes sicherstellen: `METHOD`, `CSRF`, `VALIDATION`, `SERVER`
- [ ] Backend-Validierung vervollständigen — alle Pflichtfelder und Selects geprüft
- [ ] Maskierung sensibler Daten im Mail-Body konsequent umsetzen
- [ ] Issue #6 auf GitHub schließen

---

## Danach: Paket B

- Feldbezogene Fehleranzeige (Server → Field-Mapping, `aria-describedby`)
- Scroll-to-first-error
- Fokus-Management bei Fehler & Erfolg
- Screenreader-Test SEPA-Form

## Danach: Paket C

- Rate Limiting (Session-basiert)
- IBAN Live-Validierung im Frontend

## Zurückgestellt

- **Issue #1** (SEPA rechtlich): Wartet auf Juristencheck / Pflichttexte
- **Issue #5** (Logging): Wartet auf Entscheidung zu Request-ID
