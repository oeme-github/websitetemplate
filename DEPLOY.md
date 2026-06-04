# Deployment-Anleitung

Dieses Dokument beschreibt die Ersteinrichtung auf einem VPS sowie das Einspielen von Updates.

---

## Voraussetzungen

| Was | Details |
|-----|---------|
| Server | Ubuntu 22.04+ oder Debian 12+, frische Installation |
| Zugang | SSH-Zugang mit sudo-Rechten |
| Domain | A-Record zeigt bereits auf die Server-IP (Pflicht für SSL) |
| Repo | Eigenes privates GitHub-Repo, das auf diesem Template basiert |

---

## Ersteinrichtung (`setup.sh`)

### 1. Auf dem Server einloggen

```bash
ssh user@<server-ip>
```

### 2. Setup-Skript aus dem Repo holen

Das Skript kann direkt über GitHub heruntergeladen werden — der SSH-Key für das **private** Repo wird im nächsten Schritt eingerichtet:

```bash
# Temporär per HTTPS herunterladen (einmalig, kein Auth nötig wenn öffentlich)
# ODER: Skript per scp vom lokalen Rechner übertragen
scp setup/setup.sh user@<server-ip>:/tmp/setup.sh
```

Oder alternativ: Skript-Inhalt manuell anlegen und ausführen.

> **Tipp:** Nach dem ersten Setup liegt das Skript dauerhaft unter `/var/www/<domain>/setup/setup.sh`.

### 3. Setup ausführen

```bash
sudo bash /tmp/setup.sh
```

Das Skript fragt interaktiv nach:

| Eingabe | Beispiel |
|---------|---------|
| Domain | `example.com` |
| Git-Repository (SSH) | `git@github.com:user/mein-projekt.git` |
| E-Mail für Let's Encrypt | `admin@example.com` |

### 4. Deploy Key auf GitHub hinterlegen

Das Skript erzeugt einen SSH-Key und zeigt den öffentlichen Schlüssel an. Diesen in GitHub hinterlegen:

```
Repository → Settings → Deploy Keys → Add deploy key
```

- **Title:** z.B. `VPS example.com`
- **Key:** Den angezeigten Public Key einfügen
- **Allow write access:** Nein (nur lesend nötig)

Danach im Skript mit `j` bestätigen — es testet die Verbindung automatisch.

### 5. .env konfigurieren

Das Skript führt durch die wichtigsten Variablen mit Defaults aus `.env.example`.
Alle Werte können später in `/var/www/<domain>/.env` bearbeitet werden.

**Wichtig:** Die `.env` wird nie in Git eingecheckt.

### 6. Ergebnis

Nach erfolgreichem Durchlauf:

- Website erreichbar unter `https://<domain>`
- SSL-Zertifikat aktiv, HTTP→HTTPS-Weiterleitung eingerichtet
- Zertifikat wird automatisch erneuert (Certbot-Cronjob)

---

## Updates einspielen (`update.sh`)

Lokal entwickeln, pushen, dann auf dem Server:

```bash
ssh user@<server-ip>
cd /var/www/<domain>
bash setup/update.sh
sudo systemctl reload apache2
```

Was `update.sh` tut:
- `git pull` — aktuellen Stand vom Repo holen
- `composer install --no-dev` — PHP-Abhängigkeiten aktualisieren

**Die `.env` wird dabei nicht verändert.**

---

## Ablauf: Neue Seite einrichten

```
1. Template-Repo forken / neues Repo anlegen
2. Lokal anpassen (Design, Inhalte, .env.example befüllen)
3. Auf GitHub pushen
4. VPS: sudo bash setup.sh
5. Fertig — spätere Änderungen via update.sh
```

---

## Troubleshooting

### SSL schlägt fehl

Certbot benötigt eine Domain die per DNS auf den Server zeigt. Prüfen:

```bash
dig +short example.com
curl -s https://api.ipify.org   # eigene Server-IP
```

SSL nachträglich einrichten:

```bash
sudo certbot --apache -d example.com
```

### `git pull` fragt nach Passwort

SSH-Key ist nicht als Deploy Key auf GitHub hinterlegt oder nicht der richtige Key.
Prüfen:

```bash
ssh -T git@github.com
# Erwartet: "Hi user! You've successfully authenticated..."
```

### PHP-Modul für Apache fehlt oder ist nicht aktiv

Symptom: PHP-Dateien werden als Plaintext ausgeliefert oder Apache gibt einen 500er zurück.

Prüfen, welche PHP-Version installiert ist und ob das Modul aktiv ist:

```bash
php -v
sudo a2query -m | grep php
```

Falls kein PHP-Modul aktiv ist:

```bash
sudo apt install libapache2-mod-php8.4   # Versionsnummer anpassen
sudo a2enmod php8.4
sudo systemctl reload apache2
```

> `setup.sh` installiert `libapache2-mod-php` und aktiviert das Modul automatisch. Dieser Schritt ist nur nötig, wenn die Einrichtung manuell erfolgt oder der Paketname auf dem Zielsystem abweicht.

### Apache startet nicht

Konfiguration prüfen:

```bash
sudo apache2ctl configtest
sudo journalctl -u apache2 --no-pager -n 30
```

### .env fehlt nach Clone

```bash
cp /var/www/<domain>/.env.example /var/www/<domain>/.env
nano /var/www/<domain>/.env
sudo systemctl reload apache2
```

### Dateirechte nach manuellem git pull falsch

```bash
sudo chown -R $USER:www-data /var/www/<domain>
sudo find /var/www/<domain> -type d -exec chmod 750 {} \;
sudo find /var/www/<domain> -type f -exec chmod 640 {} \;
sudo chmod +x /var/www/<domain>/setup/*.sh
```

---

## Zertifikat-Verlängerung

Certbot richtet automatisch einen Cronjob ein. Manuell testen:

```bash
sudo certbot renew --dry-run
```

---

## Ausblick: GitHub Actions

Updates können später automatisiert werden: Push auf `main` → GitHub Action → SSH auf VPS → `update.sh`. Die Skript-Struktur ist dafür bereits vorbereitet.
