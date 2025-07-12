<?php

/* Cronjob installieren */
if (rex_addon::get('cronjob') && rex_addon::get('cronjob')->isAvailable()) {
    $cronjob = array_filter(rex_sql::factory()->getArray("SELECT * FROM rex_cronjob WHERE `type` = 'rex_cronjob_yform_auto_delete'"));
    if (!$cronjob) {
        $query = rex_file::get(__DIR__ . '/install/rex_cronjob_yform_auto_delete.sql');
        rex_sql::factory()->setQuery($query);
    }
}
