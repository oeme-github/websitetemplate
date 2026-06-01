# Backlog – One-Pager Website Template

## Letzter Stand

**Version:** v1.5.0 + Deployment-Infrastruktur (gepusht)  
**Zuletzt abgeschlossen:** VPS-Setup, README-Umbau, Lizenz, Copyright-Muster

### Abgeschlossen in letzten Sessions

**Deployment-Infrastruktur**
- `setup/setup.sh`, `setup/update.sh`, `setup/apache/vhost.conf.tpl`, `DEPLOY.md`

**README, Lizenz & Copyright**
- README auf Workflow-first umgestellt, GitHub Template aktiviert
- Lizenz MIT → EUPL v1.2
- `content/legal/copyright.json` + Footer dynamisch

**Testzahlen gesamt:**
- PHP Unit: 55 Tests · JS Jest: 101 Tests · Integration: 7 Tests

---

## Nächste Session: Paket D — Content-Infrastruktur

Abgleich mit `friendsofthehawks`: Template-seitig wurden viele Verbesserungen nie zurückgepflegt.

### Paket D1 — Content-Infrastruktur (Priorität: hoch)

Alle Sektionen datengetrieben machen, kein Hardcode mehr in PHP-Templates.

- [ ] `content/home/features.md` anlegen (Placeholder-Text für Features-Sektion)
- [ ] `content/home/contact.md` anlegen (Intro-Text über Formular)
- [ ] `content/home/stats.json` anlegen (`[{value, label}]`)
- [ ] `content/home/stats.md` anlegen (Text unter Stats-Grid)
- [ ] `content/home/about-cards.json` anlegen (4 Karten mit `svg`, `title`, `text`)
- [ ] `content/home/topbar-links.json` anlegen (externe Links in Topbar, Placeholder leer)
- [ ] `content/home/videos.json` anlegen (`enabled: false` Placeholder)
- [ ] `content/home/gallery.json` auf `{file, alt, category}`-Format aktualisieren + 4 Placeholder-Einträge
- [ ] Placeholder-Bilder in `content/images/content/` auf generische Namen umbenennen (`placeholder-1.jpg` etc.)
- [ ] `templates/pages/home.php` komplett umstrukturieren:
  - Alle Sektionen via `$md()` / `$gallery()`
  - Galerie: `<picture>` mit `.webp` + `.jpg`-Fallback
  - Stats-Grid aus `stats.json`
  - About-Cards aus `about-cards.json`
  - Video-Block aus `videos.json` (mit `enabled`-Flag)
  - Contact-Intro aus `contact.md`
- [ ] `templates/partials/header.php` — Topbar-Links aus `topbar-links.json` statt hardcoded
- [ ] `public/assets/videos/` Verzeichnis + `README.md` (yt-dlp/ffmpeg-Anleitung)
- [ ] `public/assets/css/main.css` — CSS für `.section-video` und `.section-video-caption`
- [ ] `.gitignore` — `*.mp4`, `*.webm`, `*.mov` unter `public/assets/videos/` ergänzen

### Paket D2 — Accessibility-Fixes (Priorität: hoch)

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
