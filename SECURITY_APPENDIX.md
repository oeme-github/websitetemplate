# SECURITY_APPENDIX.md

## Zweck
Dieser Appendix beschreibt relevante Bedrohungen, Angriffsvektoren
und die im Projekt umgesetzten Gegenmaßnahmen. Er ergänzt die
DESIGN_PATTERN.md um eine praxisnahe Security-Betrachtung.

---

## A. Bedrohungsmodell (Threat Model)

### Schutzwürdige Ziele
- Formular-Endpunkte (Kontaktformular)
- Session-Identität
- E-Mail-Infrastruktur
- Server-Informationen
- Nutzerdaten (Name, E-Mail, Nachricht)

### Angreifer-Typen
- Ungezielte Bots (Spam, Scans)
- Gezielte Formular-Spammer
- Script-Kiddies
- Opportunistische Angreifer (Standard-Exploits)

---

## B. Angriffsklassen & Gegenmaßnahmen

### 1. Cross-Site Request Forgery (CSRF)

**Angriff:**
Ein externer Angreifer erzwingt einen POST-Request im Kontext einer bestehenden Session.

**Gegenmaßnahmen:**
- Sessionbasierte CSRF-Tokens
- Kryptografisch zufällige Tokens
- Serverseitige Prüfung vor Zugriff auf `$_POST`
- Token-Regeneration nach erfolgreicher Verarbeitung

**Status:** vollständig mitigiert

---

### 2. Bot- & Spam-Angriffe

**Angriff:**
Automatisierte Formulareinsendungen (Spam, DoS-light).

**Gegenmaßnahmen:**
- Honeypot-Feld (nicht sichtbar)
- Stiller Abbruch bei Befüllung
- Keine Rückmeldung an Bots (immer HTTP 200)

**Status:** vollständig mitigiert

---

### 3. Cross-Site Scripting (XSS)

**Angriff:**
Einschleusen von HTML/JS über Formularfelder oder URL-Parameter.

**Gegenmaßnahmen:**
- Zentrales Output-Escaping (`e()`)
- Keine direkte Ausgabe von User-Input
- Content-Security-Policy (`script-src 'self'`)
- Keine Inline-Skripte erforderlich

**Status:** vollständig mitigiert

---

### 4. Header Injection / Mail Spoofing

**Angriff:**
Manipulation von E-Mail-Headern oder Absender-Adressen.

**Gegenmaßnahmen:**
- Einsatz von PHPMailer
- Feste `From`-Adresse
- User-E-Mail ausschließlich als `Reply-To`
- SMTP statt `mail()`

**Status:** vollständig mitigiert

---

### 5. Information Leakage

**Angriff:**
Auslesen von PHP-Version, Server-Version, internen Pfaden oder Stacktraces.

**Gegenmaßnahmen:**
- Entfernen von `X-Powered-By`
- Apache `ServerTokens Prod`
- Generische Fehlermeldungen
- Fehlerlogging nur serverseitig

**Status:** vollständig mitigiert

---

### 6. Session Fixation & Hijacking

**Angriff:**
Übernahme oder Wiederverwendung einer Session-ID.

**Gegenmaßnahmen:**
- `use_strict_mode`
- Cookies-only Sessions
- `HttpOnly`, `SameSite=Lax`
- Keine Session-IDs in URLs

**Status:** vollständig mitigiert

---

### 7. Direct File Access

**Angriff:**
Direkter Zugriff auf interne PHP-Dateien (`src/`, `templates/`).

**Gegenmaßnahmen:**
- Webroot zeigt ausschließlich auf `public/`
- Single Entry Point
- Keine Logik in Templates

**Status:** vollständig mitigiert

---

## C. Nicht-Ziele (bewusste Entscheidungen)

Nicht Teil des Projekts:
- Benutzer-Authentifizierung
- Rollen & Rechte
- Rate-Limiting
- Captcha / externe Anti-Bot-Dienste
- WAF / Reverse Proxy
- Verschlüsselung gespeicherter Daten

Diese Punkte können später ergänzt werden, ohne die Architektur zu brechen.

---

## D. Security-Review-Leitfragen

- Erhöht die Änderung die Angriffsfläche?
- Wird Security serverseitig durchgesetzt?
- Können Fehlermeldungen einem Angreifer helfen?
- Wird User-Input ungefiltert ausgegeben?
- Werden neue Secrets eingeführt?

Wenn eine Frage nicht klar mit „Nein“ beantwortet werden kann,
ist ein Review erforderlich.

---

## E. Fazit

Die aktuelle Architektur bietet:
- soliden Basisschutz gegen gängige Webangriffe
- klare Verantwortlichkeiten
- gute Erweiterbarkeit
- Verständlichkeit ohne Framework-Abhängigkeit

Security ist integraler Bestandteil des Designs.
