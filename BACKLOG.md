# Backlog – One-Pager Website Template

## Letzter Stand

**Version:** v1.5.0 + Deployment-Infrastruktur (gepusht)  
**Zuletzt abgeschlossen:** VPS-Setup, README-Umbau, Lizenz, Copyright-Muster

### Abgeschlossen in dieser Session

**Deployment-Infrastruktur**
- `setup/setup.sh` — interaktives VPS-Ersteinrichtungs-Skript (Pakete, SSH-Key, Clone, Apache, SSL, .env, Composer)
- `setup/update.sh` — `git pull` + `composer install --no-dev` + Hinweis zu Apache-Reload
- `setup/apache/vhost.conf.tpl` — Apache-vHost-Template mit `__DOMAIN__` / `__REPO_PATH__`
- `DEPLOY.md` — vollständige Deployment-Anleitung inkl. Troubleshooting

**README umstrukturiert**
- „Schnellstart: Von Template zur laufenden Seite" steht jetzt oben
- Workflow-Tabelle „Was anpassen / Wo"
- Technische Details nach unten verschoben
- GitHub-Repo als Template-Repository markiert (→ „Use this template"-Button)

**Lizenz & Copyright**
- Lizenz von MIT auf **EUPL v1.2** umgestellt (offizieller EU-Text)
- `content/legal/copyright.json` neu — konfigurierbarer Copyright-Inhaber
- `templates/partials/footer.php` — Copyright dynamisch aus JSON, Jahreszahl automatisch

**Testzahlen gesamt (unverändert):**
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
