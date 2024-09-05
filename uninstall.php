<?php

// in der uninstall.php sollten Befehle ausgeführt werden, die alle Änderungen, die mit der Installation kamen, entfernen.

// Konfiguration entfernen
// rex_config::removeNamespace("auto_delete");

// Installierte Metainfos entfernen
// rex_metainfo_delete_field('art_auto_delete');
// rex_metainfo_delete_field('cat_auto_delete');
// rex_metainfo_delete_field('med_auto_delete');
// rex_metainfo_delete_field('clang_auto_delete');

// Zusäzliche Verzeichnisse entfernen, z.B.
// rex_dir::delete(rex_path::get('auto_delete'), true);

// YForm-Tabellen löschen (die YForm-Tabellendefinition wird gelöscht, nicht die Datenbank-Tabellen)
// rex_yform_manager_table_api::removeTable('rex_auto_delete');

// Weitere Vorgänge
// ...
