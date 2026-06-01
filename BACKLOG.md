# Backlog – One-Pager Website Template

## Letzter Stand

**Version:** v1.5.0 + Deployment + Paket D1 (gepusht)  
**Zuletzt abgeschlossen:** Paket D1 — Content-Infrastruktur vollständig

### Abgeschlossen in dieser Session

**Paket D1 — Content-Infrastruktur**
- `content/home/features.md`, `contact.md`, `stats.json`, `stats.md`, `about-cards.json`, `topbar-links.json`, `videos.json` — alle neu angelegt
- `content/home/gallery.json` — auf `{file, alt, category}` umgestellt, 4 Placeholder-Einträge
- Bilder: 4 umbenannt auf `placeholder-1..4.jpg`, 7 projektspezifische gelöscht
- `templates/pages/home.php` — komplett datengetrieben: `$md()` / `$gallery()`, `<picture>`+webp, Stats/Cards/Video aus JSON
- `templates/partials/header.php` — Topbar-Links aus JSON, doppelte Security-Header entfernt
- `main.css` — CSS für Topbar-Links, `.section-video`, `.about-cards` inkl. Dark-Mode + Responsive
- `public/assets/videos/README.md` — yt-dlp/ffmpeg-Anleitung
- `.gitignore` — Videodateien ausgeschlossen

**Testzahlen gesamt (unverändert):**
- PHP Unit: 55 Tests · JS Jest: 101 Tests · Integration: 7 Tests

---

## Nächste Session: Paket D2 + D3

### Paket D2 — Accessibility-Fixes (Priorität: hoch) ← nächste Session

Aus `friendsofthehawks` zurückgepflegte Bugfixes.

- [ ] `templates/partials/header.php` — doppelte Security-Header entfernen (macht `index.php` bereits)
- [ ] `templates/partials/header.php` — `aria-hidden="true"` + `tabindex="-1"` von `.nav-mobile` entfernen
- [ ] `public/assets/css/main.css` — `.nav-mobile`: `visibility: hidden` im geschlossenen Zustand (statt aria-hidden-Hack)
- [ ] `public/assets/css/main.css` — `.lightbox`: `visibility: hidden` im geschlossenen Zustand
- [ ] `templates/pages/home.php` — hardcodiertes `aria-hidden="true"` vom Lightbox-Container entfernen

### Paket D3 — SEPA-Verbesserungen (Priorität: mittel)

- [ ] `templates/partials/forms/sepa.php` — Tippfehler `Qurtal` → `Quartal` beheben
- [ ] `templates/partials/forms/sepa.php` — neue Felder: `geburtsdatum` (Pflicht), `telefon` + `herkunft` (optional)
- [ ] `public/send_sepa.php` — neue Felder einlesen + validieren (`geburtsdatum` mit `checkdate()`)
- [ ] `src/Helpers/SepaPdf.php` — Felder maschinenlesbar (`Vorname:`, `Nachname:`, `Strasse:` etc. einzeln)
- [ ] `src/Helpers/SepaPdf.php` — dynamischer PDF-Titel (Spende vs. Mitgliedschaft)
- [ ] `public/send_sepa.php` — dynamischer E-Mail-Betreff (Neue Spende / Neue Mitgliedschaft)
- [ ] `public/send_sepa.php` — PDF-Dateiname eindeutig: `{mandateId}.pdf` statt `SEPA-Mandat.pdf`

---

## Offene Issues (GitHub)

| # | Titel | Priorität | Status |
|---|-------|-----------|--------|
| #1 | SEPA-Flow rechtlich finalisieren | Offen | Wartet auf User-Input |
| #5 | Logging-Strategie (DEV vs PROD) | Offen | Wartet auf User-Input |

---

## Zurückgestellt

- **Screenreader-Test SEPA-Formular**: manueller Test (NVDA/VoiceOver) — kein Code-Task
- **Issue #1** (SEPA rechtlich): Wartet auf Juristencheck / Pflichttexte
- **Issue #5** (Logging): Wartet auf Entscheidung zu Request-ID
