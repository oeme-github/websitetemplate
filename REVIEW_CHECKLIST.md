# REVIEW_CHECKLIST.md

Diese Checkliste dient dazu, Änderungen im Projekt schnell und konsistent
gegen die festgelegten Design- und Architekturregeln zu prüfen.

Sie basiert vollständig auf der `DESIGN_PATTERN.md` und ist als
**Self-Check oder Review-Grundlage** gedacht.

---

## 1. Projekt- & Ordnerstruktur

- [ ] Liegt jede neue Datei im dafür vorgesehenen Ordner?
- [ ] Befindet sich ausschließlich öffentlich erreichbarer Code unter `public/`?
- [ ] Ist sichergestellt, dass `src/`, `templates/` und `vendor/` nicht direkt erreichbar sind?
- [ ] Gibt es keine neuen PHP-Dateien im Projekt-Root?

---

## 2. Entry Point & Routing

- [ ] Werden Requests ausschließlich über `public/index.php` verarbeitet?
- [ ] Existiert kein direkter URL-Zugriff auf Templates oder PHP-Klassen?
- [ ] Wird Routing über Whitelists oder feste Zuordnungen gelöst?
- [ ] Gibt es kein dynamisches Include auf Basis von User-Input?

---

## 3. PHP-Code & Composer

- [ ] Liegt PHP-Logik ausschließlich unter `src/`?
- [ ] Wird Composer-Autoload (`use App\...`) statt manueller Includes genutzt?
- [ ] Gibt es keine relativen `require`- oder `include`-Ketten?
- [ ] Sind neue Abhängigkeiten sinnvoll, minimal und über Composer eingebunden?

---

## 4. Templates & Darstellung

- [ ] Enthalten Templates ausschließlich Darstellungslogik?
- [ ] Gibt es keine Business-, Security- oder Session-Logik in Templates?
- [ ] Werden Variablen im Template nur zur Ausgabe verwendet?
- [ ] Sind Layouts, Partials und Seiten klar getrennt?

---

## 5. Output & Escaping (XSS)

- [ ] Wird jede variable Ausgabe escaped (`e()` oder äquivalent)?
- [ ] Gibt es keine direkte Ausgabe von `$_GET`, `$_POST`, `$_SESSION`?
- [ ] Ist unescaped Output explizit begründet und dokumentiert?

---

## 6. Security-Grundlagen

- [ ] Erfolgen Sicherheitsentscheidungen ausschließlich serverseitig?
- [ ] Wird JavaScript nicht zur Durchsetzung von Security verwendet?
- [ ] Gibt es keine sensiblen Daten im Frontend oder in Assets?
- [ ] Werden kritische Aktionen nicht direkt aus Templates ausgelöst?

---

## 7. Fehlerbehandlung & Debugging

- [ ] Werden Fehler nicht direkt an den Benutzer ausgegeben?
- [ ] Gibt es keine `var_dump()`, `print_r()` oder `die()`-Aufrufe in Production-Code?
- [ ] Erfolgt Debugging über Logging oder kontrollierte Mechanismen?

---

## 8. Änderungen an der Architektur

- [ ] Entspricht die Änderung den bestehenden Design-Patterns?
- [ ] Falls nicht: wurde eine neue Regel in `DESIGN_PATTERN.md` ergänzt?
- [ ] Ist die Entscheidung dokumentiert und nachvollziehbar?
- [ ] Wurde Konsistenz im gesamten Projekt geprüft?

---

## 9. Abschluss-Check

- [ ] Würde ein neuer Entwickler die Struktur intuitiv verstehen?
- [ ] Ist die Änderung sicherer oder zumindest nicht unsicherer als zuvor?
- [ ] Würde die Änderung auch in 6 Monaten noch Sinn ergeben?

---

## Hinweis

Diese Checkliste ersetzt kein Denken,
sondern unterstützt dabei, **bewusst und konsistent** zu entscheiden.

Wenn mehrere Punkte mit „Nein“ beantwortet werden,
sollte die Änderung überarbeitet werden.
