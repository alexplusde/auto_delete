<?php

// in der uninstall.php sollten Befehle ausgeführt werden, die alle Änderungen, die mit der Installation kamen, entfernen.

// Cronjobs deaktivieren
if (rex_addon::get('cronjob')->isAvailable()) {
}   
