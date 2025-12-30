## v1.1.0 – Legal Pages & Layout Refinement

### Added
- New legal pages: Impressum and Datenschutzerklärung
- Consistent global header and footer across all pages
- Footer redesigned using responsive CSS Grid

### Improved
- Legal content layout with dedicated `.section-legal`
- Removed scroll-snap from legal pages
- Improved mobile readability for long headings
- Reduced footer height on mobile
- Mobile viewport handling using modern `svh/dvh`

### Quality
- Lighthouse: 100 / 100 / 100 / ~98
- No breaking changes

---

## v1.0.0 – Initial Release

Erste stabile Version des One-Pager-Templates.

### Highlights
- Barrierearmer One-Pager mit klarer HTML-Struktur
- Responsive Header mit Desktop- & Mobile-Navigation
- Barrierefreies Mobile-Menü mit korrektem ARIA-State
- Kontaktformular mit:
  - CSRF-Schutz
  - Honeypot gegen Bots
  - serverseitiger Validierung
  - AJAX-Submit mit ARIA-Feedback
- Dark-/Light-Theme über CSS Custom Properties
- Saubere Trennung von HTML, CSS, JavaScript und PHP

### Qualität & Standards
- Lighthouse:
  - Performance: 98
  - Accessibility: 100
  - Best Practices: 100
  - SEO: 100
- Frameworkfreies Setup (Vanilla JS / PHP)
- PHP ≥ 8 mit Composer (PHPMailer)
- Sichere PHP-Endpunkte via `.htaccess`

### Dokumentation
- Vollständige README mit Deployment-Hinweisen
- CONTRIBUTING.md für externe Beiträge
- MIT License

### Hinweise
Dieses Release dient als stabiler Ausgangspunkt.
Anpassungen an Design, Inhalt oder Struktur sollten stets mit einer erneuten Accessibility-Prüfung einhergehen.

---

**Status:** Stable
