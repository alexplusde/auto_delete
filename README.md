
# Auto-Delete-Addon für REDAXO 5.x

Löscht alte Logs, Dateien und Datensätze (mit und ohne YForm) via Cronjob.

Wird zur Unterstützung von DSGVO und GDPR-Kompatiblität verwendet in:

* <https://github.com/friendsofredaxo/warehouse/>
* <https://github.com/alexplusde/events>
* <https://github.com/alexplusde/stellenangebote/>

## Features

### YForm-Feld `datestamp_auto_delete`

Ein Feld für YForm, das wie `datestamp` beim Erstellen oder Updaten eines Datensatzes einen Zeitstempel in der Zukunft erzeugt, der als Referenz für den passenden Lösch-Cronjob herangezogen wird.

[Liste der möglichen Offset-Parameter in den PHP-Docs](https://www.php.net/manual/de/function.strtotime.php)

### Cronjob `YForm`

Das passende Gegenstück zu `datestamp_auto_delete` für YForm. Durchsucht alle in YForm verwalteten Tabellen nach dem Feld `datestamp_auto_delete` und löscht den Datensatz auf Basis von YOrm unter Berücksichtigung der jeweiligen Extension Points.

### Cronjob `Folder`

Durchsucht ein angegebenes Verzeichnis nach Daten, die älter sind als ein gewünschter Zeitpunkt und löscht diese. Vergleichbar mit dem von REDAXO mitgeliefertem Cronjob für PHPMailer-Logs oder Datenbank-Sicherungen, jedoch für ein frei wählbares Verzeichnis.

Z.B. Ordner, in die der Nutzer über YForm im Frontend Daten hochlädt, denkbar bei Bewerberformularen.

> **Vorsicht:** Falsch angegebene Pfade können zu ungewolltem Datenverlust führen. Bitte die Pfadangaben vorher überprüfen.

### Cronjob `Table`

> **Hinweis:** nur empfohlen für Tabellen, die **nicht** in YForm verwaltet werden.

Ein Cronjob, der ein beliebiges Feld einer beliebigen Tabelle heranzieht (z.B. `updatedate`) und nach einem festgelegten Zeitabstand, z.B. `+ 3 months` die Daten darin löscht.

## Lizenz

MIT Lizenz, siehe [LICENSE.md](https://github.com/alexplusde/auto_delete/blob/master/LICENSE.md)  

## Autoren

**Alexander Walther**  
<http://www.alexplus.de>  
<https://github.com/alexplusde>  

**Projekt-Lead**  
[Alexander Walther](https://github.com/alexplusde)
