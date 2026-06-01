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

## Nächste Session

Paket D vollständig abgeschlossen. Offene Punkte:

### Paket D2 — Accessibility-Fixes ✅ abgeschlossen

- [x] `header.php` doppelte Security-Header entfernt (D1)
- [x] `header.php` `aria-hidden` + `tabindex` von `.nav-mobile` entfernt (D1)
- [x] `.lightbox` CSS `visibility: hidden` (war bereits vorhanden)
- [x] Lightbox-HTML kein hardcodiertes `aria-hidden` (D1)
- [x] `.nav-mobile` CSS `visibility: hidden` + Transition-Timing

### Paket D3 — SEPA-Verbesserungen ✅ abgeschlossen

- [x] Tippfehler `Qurtal` → `Quartal` (Formular + Whitelist)
- [x] Neue Felder: `geburtsdatum` (Pflicht, type=date), `telefon` + `herkunft` (optional)
- [x] `send_sepa.php` — `geburtsdatum` einlesen, YYYY-MM-DD → DD.MM.YYYY, `checkdate()` Validation
- [x] `SepaPdf.php` — Felder maschinenlesbar, eigene Sections (Antragsteller/Mitgliedschaft/Bank/Mandat)
- [x] `SepaPdf.php` — dynamischer Titel (Spende vs. Mitgliedsantrag), XSS-Escaping via `esc()`
- [x] `send_sepa.php` — dynamischer E-Mail-Betreff
- [x] `send_sepa.php` — PDF-Dateiname `{mandateId}.pdf`
- [x] Integrations-Test aktualisiert (neues Pflichtfeld, Betreff, Dateinamen-Pattern)

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
