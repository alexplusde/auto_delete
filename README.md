
# Auto-Delete-Addon für REDAXO 5.x

![image](https://user-images.githubusercontent.com/3855487/152675689-328899a4-90d6-41da-bef4-78d1c8e7f8c5.png)

Löscht alte Logs und Datensätze via Cronjob.

## Features

### YForm-Feld `datestamp_auto_delete`

Ein Feld für YForm, das wie `datestamp` beim Erstellen oder Updaten eines Datensatzes einen Zeitstempel in der Zukunft erzeugt, der als Referenz für den passenden Lösch-Cronjob herangezogen wird.

[Liste der möglichen Offset-Parameter in den PHP-Docs](https://www.php.net/manual/de/function.strtotime.php)

### Cronjob `yform_auto_delete`

Das passende Gegenstück zu `datestamp_auto_delete` für YForm. Durchsucht alle in YForm verwalteten Tabellen nach dem Feld `datestamp_auto_delete` und löscht den Datensatz auf Basis von YOrm unter Berücksichtigung der jeweiligen Extension Points.

### Cronjob `folder_auto_delete` 

Durchsucht ein angegebenes Verzeichnis nach Daten, die älterer sind als ein gewünschter Zeitpunkt und löscht diese. Vergleichbar mit dem von REDAXO mitgeliefertem Cronjob für PHPMailer-Logs oder Datenbank-Sicherungen, jedoch für ein frei wählbares Verzeichnis. 

Z.B. Ordner, in die der Nutzer über YForm im Frontend Daten hochlädt, denkbar bei Bewerberformularen.

> **Vorsicht:** Falsch angegebene Pfade können zu ungewolltem Datenverlust führen. Bitte die Pfadangaben vorher überprüfen.

### Cronjob `table_auto_delete`

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
