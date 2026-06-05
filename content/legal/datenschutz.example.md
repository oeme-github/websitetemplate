# Datenschutzerklärung

## Verantwortlicher

Verantwortlicher im Sinne der DSGVO:

**[Vor- und Nachname oder Organisationsname]**  
[Straße und Hausnummer]  
[PLZ Ort]  
[Land]

E-Mail: [datenschutz@beispiel.de]  
Telefon: [+49 ...]

---

## 1. Allgemeine Hinweise

Diese Website verarbeitet personenbezogene Daten nur im technisch notwendigen Umfang. Es werden keine Tracking-Pixel, Analyse-Dienste (wie Google Analytics) oder Werbe-Cookies eingesetzt.

---

## 2. Server-Protokolldaten

Beim Abruf dieser Website speichert der Webserver automatisch folgende Daten in Protokolldateien:

- IP-Adresse des anfragenden Geräts
- Datum und Uhrzeit der Anfrage
- URL der aufgerufenen Seite
- HTTP-Statuscode
- Übertragene Datenmenge
- Referrer (zuvor besuchte Seite, sofern übermittelt)
- Browser und Betriebssystem (User-Agent)

**Rechtsgrundlage:** Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse an Betrieb und Sicherheit der Website).

**Speicherdauer:** Protokolldateien werden nach spätestens 14 Tagen gelöscht oder anonymisiert, sofern kein Sicherheitsvorfall eine längere Aufbewahrung erfordert.

**Hosting:** Diese Website wird gehostet bei [Name und Adresse des Hosting-Anbieters, z. B. Hetzner Online GmbH, Industriestr. 25, 91710 Gunzenhausen]. Mit dem Hoster besteht ein Auftragsverarbeitungsvertrag gemäß Art. 28 DSGVO.

---

## 3. Session-Cookie (technisch notwendig)

Diese Website setzt beim Aufruf ein Session-Cookie. Das Cookie enthält ausschließlich eine zufällige Sitzungs-ID und dient dem Schutz vor Cross-Site-Request-Forgery (CSRF-Schutz). Es werden keine personenbezogenen Inhalte im Cookie gespeichert.

- **Cookie-Name:** PHP-Session-Cookie (standardmäßig `PHPSESSID`)
- **Speicherort:** Browser des Nutzers
- **Lebensdauer:** Wird beim Schließen des Browsers gelöscht (Session-Cookie)
- **Übertragung:** Nur an diese Website (`SameSite=Lax`, `HttpOnly`)

**Rechtsgrundlage:** Art. 6 Abs. 1 lit. f DSGVO. Ein Einwilligungsbanner ist für technisch notwendige Cookies nicht erforderlich (§ 25 Abs. 2 Nr. 2 TTDSG).

---

## 4. Browser-Speicher (localStorage)

Diese Website speichert im lokalen Speicher Ihres Browsers (localStorage) folgende Einstellungen:

- Gewähltes Farbschema (z. B. „Standard", „Warm", „Natur")
- Gewähltes Anzeigedesign (hell / dunkel)
- Status des Cookie-Hinweises (ob Sie diesen bereits bestätigt haben)

Diese Daten sind nicht personenbezogen, verlassen Ihren Browser nicht und werden nicht an den Server übertragen. Sie können den localStorage jederzeit über die Entwicklertools Ihres Browsers löschen.

---

## 5. Kontaktformular

> ℹ️ Dieser Abschnitt ist relevant, wenn `FORM_TYPE=contact` gesetzt ist.

Wenn Sie das Kontaktformular nutzen, werden folgende Daten verarbeitet:

- Ihr Name
- Ihre E-Mail-Adresse
- Ihr Nachrichtentext

Die Daten werden ausschließlich zur Bearbeitung Ihrer Anfrage per E-Mail an den Verantwortlichen übertragen. Eine Speicherung in einer Datenbank findet nicht statt. Die E-Mail wird beim Empfänger so lange aufbewahrt, wie es zur Bearbeitung Ihrer Anfrage erforderlich ist.

**Rechtsgrundlage:** Art. 6 Abs. 1 lit. b DSGVO (Anbahnung oder Erfüllung eines Vertrags) bzw. lit. f (berechtigtes Interesse an der Kommunikation).

**Hinweis:** Ihre E-Mail-Adresse wird ausschließlich als Antwortadresse (`Reply-To`) verwendet. Die versendende E-Mail-Adresse gehört dem Websitebetreiber.

---

## 6. SEPA-Lastschriftmandat

> ℹ️ Dieser Abschnitt ist relevant, wenn `FORM_TYPE=sepa` gesetzt ist.

Wenn Sie das SEPA-Formular nutzen, werden folgende Daten zur Erstellung eines Lastschriftmandats verarbeitet:

- Vor- und Nachname
- Anschrift
- Geburtsdatum
- IBAN (Internationale Bankkontonummer)
- Bankname (automatisch ermittelt, siehe Abschnitt 7)
- Telefonnummer (optional)
- Herkunft (optional)
- Mitgliedschafts- oder Spendendetails (Betrag, Intervall, Quartal)

Die Daten werden zur Erstellung eines PDF-Mandats verwendet, das per E-Mail an den Verantwortlichen übermittelt wird. Eine Speicherung in einer Datenbank findet nicht statt.

**Rechtsgrundlage:** Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung).

**Speicherdauer:** Das Mandat unterliegt den gesetzlichen Aufbewahrungsfristen. Für steuerlich relevante Belege beträgt die Aufbewahrungsfrist gemäß § 147 AO in der Regel 10 Jahre.

---

## 7. Bankname-Ermittlung über openiban.com

> ℹ️ Dieser Abschnitt ist relevant, wenn `FORM_TYPE=sepa` gesetzt ist.

Zur automatischen Ermittlung des Banknamens anhand der eingegebenen IBAN wird diese an den externen Dienst **openiban.com** übermittelt. Es werden keine weiteren personenbezogenen Daten übertragen.

- **Anbieter:** openiban.com (Drittanbieter)
- **Übermittelte Daten:** IBAN
- **Zweck:** Anzeige des Banknamens als Orientierungshilfe für den Nutzer

**Rechtsgrundlage:** Art. 6 Abs. 1 lit. b DSGVO (Vertragsanbahnung).

Sofern openiban.com Daten außerhalb des Europäischen Wirtschaftsraums (EWR) verarbeitet, erfolgt die Übermittlung auf Basis der Standardvertragsklauseln der EU-Kommission (Art. 46 Abs. 2 lit. c DSGVO).

> **Hinweis für Betreiber:** Prüfen Sie die aktuellen Datenschutzhinweise von openiban.com und passen Sie diesen Abschnitt ggf. an.

---

## 8. Ihre Rechte

Sie haben gegenüber dem Verantwortlichen folgende Rechte hinsichtlich Ihrer personenbezogenen Daten:

- **Auskunft** (Art. 15 DSGVO)
- **Berichtigung** (Art. 16 DSGVO)
- **Löschung** (Art. 17 DSGVO)
- **Einschränkung der Verarbeitung** (Art. 18 DSGVO)
- **Datenübertragbarkeit** (Art. 20 DSGVO)
- **Widerspruch** (Art. 21 DSGVO)

Zur Wahrnehmung Ihrer Rechte wenden Sie sich bitte an: [datenschutz@beispiel.de]

---

## 9. Beschwerderecht

Sie haben das Recht, sich bei einer Datenschutz-Aufsichtsbehörde über die Verarbeitung Ihrer personenbezogenen Daten zu beschweren. Die zuständige Aufsichtsbehörde richtet sich nach dem Bundesland des Verantwortlichen.

Eine Liste der Aufsichtsbehörden in Deutschland finden Sie unter:  
[https://www.bfdi.bund.de/DE/Infothek/Anschriften_Links/anschriften_links-node.html](https://www.bfdi.bund.de/DE/Infothek/Anschriften_Links/anschriften_links-node.html)

---

## 10. Aktualität

Diese Datenschutzerklärung wurde zuletzt aktualisiert am: [TT.MM.JJJJ]

Wir behalten uns vor, diese Erklärung bei Änderungen der Website oder der Rechtslage anzupassen.
