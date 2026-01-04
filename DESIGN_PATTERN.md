# DESIGN_PATTERN.md

## Zweck dieses Dokuments

Dieses Dokument beschreibt die grundlegenden Design- und Architekturregeln
für dieses Projekt.

Ziel ist:
- klare Zuständigkeiten
- hohe Wartbarkeit
- saubere Sicherheitsgrenzen
- Vermeidung von impliziten Annahmen und Wildwuchs

Es handelt sich **nicht um ein Framework**, sondern um verbindliche
Projektregeln für PHP-Projekte mit Composer **ohne Frameworks**.

---

## 1. Single Entry Point

### Regel
Alle HTTP-Requests laufen ausschließlich über `public/index.php`.

### Begründung
- verhindert Direktzugriffe auf interne Dateien
- ermöglicht zentrale Kontrolle über Routing und Security
- reduziert Angriffsfläche

### Erlaubt
```
/public/index.php
```

### Verboten
```
/templates/*.php per URL aufrufen
/src/*.php direkt erreichbar
```

---

## 2. Öffentlicher und nicht-öffentlicher Code

### Regel
Der Ordner `public/` ist der **einzige** Webroot.
Alle anderen Ordner sind nicht öffentlich zugänglich.

### Begründung
- schützt Konfiguration, Logik und Vendor-Code
- verhindert versehentliche Datei-Leaks

### Struktur
```
/public      → öffentlich
/src         → nicht öffentlich
/templates   → nicht öffentlich
/vendor      → nicht öffentlich
```

---

## 3. Trennung von Zuständigkeiten (Separation of Concerns)

### Regel
Jeder Hauptordner hat eine klar definierte Verantwortung.

| Ordner       | Verantwortung |
|--------------|---------------|
| `public/`    | Einstiegspunkt, Routing, Assets |
| `src/`       | PHP-Logik, Security, Helper |
| `templates/` | Darstellung (HTML) |
| `vendor/`    | Drittanbieter-Code |

### Begründung
- bessere Lesbarkeit
- einfacheres Refactoring
- klarere Sicherheitsgrenzen

---

## 4. PHP-Logik gehört nach `src/`

### Regel
Alle PHP-Logik liegt unter `src/` und wird über Composer autoloaded.

### Erlaubt
```php
use App\Security\Csrf;
```

### Verboten
```php
require '../includes/csrf.php';
```

### Begründung
- kein Include-Chaos
- eindeutige Abhängigkeiten
- bessere Testbarkeit

---

## 5. Templates sind logikfrei

### Regel
Templates enthalten **keine Business- oder Security-Logik**.

### Erlaubt
```php
<h1><?= e($title) ?></h1>
```

### Verboten
```php
<?php
if ($_SESSION['is_admin']) {
    deleteUser();
}
```

### Begründung
- verhindert Seiteneffekte
- vereinfacht Layout-Änderungen
- reduziert Security-Risiken

---

## 6. Kein dynamisches Include

### Regel
Dateien dürfen nicht dynamisch aus User-Input inkludiert werden.

### Verboten
```php
include $_GET['page'] . '.php';
```

### Erlaubt
```php
$pages = [
    'home' => 'pages/home.php',
    'about' => 'pages/about.php'
];

$template = $pages[$page] ?? 'pages/home.php';
```

### Begründung
- verhindert Local File Inclusion (LFI)
- kontrollierte Seitenstruktur

---

## 7. Output wird immer escaped

### Regel
Jede variable Ausgabe wird escaped, außer sie ist explizit als sicher definiert.

### Erlaubt
```php
<p><?= e($username) ?></p>
```

### Verboten
```php
<p><?= $_GET['name'] ?></p>
```

### Begründung
- schützt vor Cross-Site-Scripting (XSS)
- klarer Standard statt Einzelfallentscheidungen

---

## 8. Composer ist die einzige Abhängigkeitsquelle

### Regel
Externer Code wird ausschließlich über Composer eingebunden.

### Begründung
- reproduzierbare Builds
- Sicherheitsupdates möglich
- transparente Abhängigkeiten

### Verboten
- Kopieren von Libraries ins Projekt
- Vendor-Code unter `src/` oder `public/`

---

## 9. Fehlerbehandlung ohne Informationsleck

### Regel
Fehlerdetails werden niemals direkt an den Benutzer ausgegeben.

### Erlaubt
- Logging
- generische Fehlermeldungen

### Verboten
- Stacktraces im Browser
- `var_dump()` oder `print_r()` in Production

---

## 10. Änderungen an der Architektur

### Regel
Neue Struktur- oder Designentscheidungen werden:
- dokumentiert
- begründet
- konsistent angewendet

Dieses Dokument ist verbindlich für alle strukturellen Änderungen.
