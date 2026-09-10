# Backlog – One-Pager Website Template

## Letzter Stand

**Version:** v1.6.1 (gepusht)  
**Zuletzt abgeschlossen:** `content/`-`.example.*`-Mechanismus + DSGVO-Mustertext + Deployment-Warnung in `.gitignore`

### Abgeschlossen in dieser Session

**Issue #13 — `update.sh`: git pull ohne Tracking-Branch**
- `setup/update.sh` — `git pull` → `git pull origin main`

**Issue #8 — IBAN UX-Verbesserung (DSGVO)**
- `templates/partials/forms/sepa.php` — Hinweistext unter IBAN-Feld (openiban.com)
- `public/assets/css/main.css` — `.form-hint`-Klasse ergänzt
- `content/legal/datenschutz.example.md` — vollständiger Mustertext mit allen template-spezifischen Abschnitten

**Issue #7 — Rate Limiting für Formulare**
- Bereits implementiert in `src/http/FormEndpoint.php` — Issue als erledigt geschlossen

**`content/`-`.example.*`-Mechanismus**
- Alle 13 Content-Dateien umbenannt zu `*.example.*`
- `$md()` + `$gallery()` in `public/index.php` — Fallback auf `*.example.*` wenn Kundendatei fehlt
- `.gitignore` — Kundendateien ignoriert, `*.example.*` explizit ausgenommen
- `.gitignore` — prominente Warnung für Deployment-Repos mit Override-Anleitung
- `README.md` + `DESIGN_PATTERN.md` — neues Pattern dokumentiert

---

### Abgeschlossen in vorherigen Sessions

**Issue #12 — `{{VAR_NAME}}`-Platzhalter in `$md()`**
- `public/index.php` — `$md()` führt nach Markdown-Rendering denselben Placeholder-Replace-Pass durch wie `$gallery()`

**Paket D1–D3** — Content-Infrastruktur, A11y, SEPA

**Paket E — Templates/CSS/htaccess**
- `impressum.php` + `datenschutz.php` → `$md()`, LCP-Preload, Footer „Start"-Link
- CSS 600px-Breakpoint (Gallery 1-col, Lightbox nav ausblenden)
- `.htaccess` Gzip + Cache-Control

**Paket F — Hintergrundbilder + CSS-Tokens**
- `body_bg.jpg` Placeholder, `--bg-image-*`, `--color-text-hero`, `--font-display`
- body/section.alt/hero mit Hintergrundbildern + Overlays
- Dark-Mode Overlays für alle Sektionen
- Gallery 900px→2-col Breakpoint, MAIL_FROM_NAME aus .env

**Testzahlen gesamt:**
- PHP Unit: 55 Tests · JS Jest: 101 Tests · Integration: 7 Tests

---

## Offene Issues (GitHub)

Keine offenen Issues. Issue #1 (SEPA-Flow rechtlich finalisieren) und #5 (Logging-Strategie DEV
vs PROD) am 2026-09-10 geschlossen — aktueller Stand vom Betreiber als ausreichend bestätigt.

---

## Zurückgestellt

- **Screenreader-Test SEPA-Formular**: manueller Test (NVDA/VoiceOver) — kein Code-Task

---

## Nächste größere Aufgaben

### websitetemplate_D01 — CLAUDE.md auf dev-notes-Template umstellen

Eigene englische Struktur, keine STANDARDS.md-Referenz. Gemeinsam mit
`buero-desk-booking-landing`/`friendsofthehawks` zu betrachten (gemeinsame Abstammung von hier).
Zusätzlicher konkreter Fund: toter Session-End-Schritt „Windows-Kopie synchronisieren"
(`/mnt/f/git_repos/websitetemplate`) muss dabei ebenfalls entfernt werden (seit WSL-Ablösung
2026-09-02 nicht mehr funktionsfähig). Siehe `dev-notes/BACKLOG.md` D17, `dev-notes_F02`.

**Teilweise umgesetzt (2026-09-05):** `CLAUDE.md` auf dev-notes-Template umgestellt (Verweis auf
`STANDARDS.md`, Entwicklungsumgebung/Doku-Check/Verwandte-Repositories ergänzt), toter
Windows-Sync-Schritt entfernt, „Template-Einsatz"-Tabelle um `buero-desk-booking-landing`
ergänzt. **Bewusst nicht final geschrieben:** das „Local Environment"-Kapitel — der WSL2-Ersatz
für den visuellen Browser-QA-Workflow ist verprobt (`php -S` + `mcp__claude-in-chrome` über LAN,
siehe `dev-notes/projects/websitetemplate.md`, Eintrag 2026-09-03), aber die Folgefrage „saubere
URLs (Router-Skript vs. Apache-Vhost auf der Devbox)" ist noch **nicht entschieden** — braucht
eine eigene `websitetemplate`-Sitzung, bevor das Kapitel final beschrieben werden kann. `CLAUDE.md`
verweist bis dahin nur auf diesen Punkt statt einen unfertigen Workflow zu beschreiben.

### friendsofthehawks — Template-Kompatibilität herstellen

`friendsofthehawks` basiert auf dem Template, wurde aber eigenständig weiterentwickelt und hat sich vom Template entfernt. Vor einem `git merge template/main` muss geprüft werden:

- [ ] Diff zwischen `friendsofthehawks` und aktuellem `template/main` — was hat sich auseinanderentwickelt?
- [ ] Content-Dateien noch unter alten Namen (`hero.md` etc.) — Umbenennung auf `*.example.*`-Fallback-Mechanismus anpassen
- [ ] `.gitignore` — `!content/**/*.md` / `!content/**/*.json` Override ergänzen
- [ ] Template-spezifische Änderungen (Security, JS, CSS, PHP) rückportieren
- [ ] Kundenseitige Anpassungen identifizieren und sichern, damit sie beim Merge nicht überschrieben werden

### websitetemplate_D02 — Feature-Entwicklungen aus friendsofthehawks auf Rückportierung ins Template prüfen

Fund 2026-09-05 (`dev-notes`-CLAUDE.md-Konsolidierungsaudit, `friendsofthehawks_D01`): die
Abweichungen zwischen `friendsofthehawks` und diesem Template (u. a. andere Test-Anzahl,
sessionless statt session-basierte CSRF-Tokens, kein Color-Scheme-System) stammen laut User nicht
nur aus verpasstem `git merge template/main`, sondern aus **eigenen Feature-Entwicklungen in
`friendsofthehawks`**, die bisher nie mit dem Template abgeglichen wurden — andere Richtung als
die obige Checkliste (die vor allem Template→Downstream betrachtet). Zu klären, sobald der Diff
aus der Checkliste oben vorliegt: welche dieser Feature-Entwicklungen sind generisch genug, um
ins Template zurückzuwandern (damit auch `buero-desk-booking-landing`/`beatmungswg-ofterdingen`
davon profitieren), und welche sind bewusst `friendsofthehawks`-spezifisch und bleiben dort.

