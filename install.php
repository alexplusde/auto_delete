<?php

$addon = rex_addon::get('cronjob');

/* Cronjob installieren */
if (null !== $addon && rex_addon::get('cronjob')->isAvailable()) {
    $cronjob = array_filter(rex_sql::factory()->getArray("SELECT * FROM rex_cronjob WHERE `type` = 'rex_cronjob_auto_delete_yform'"));
    if (!$cronjob) {
        $query = rex_file::get(__DIR__ . '/install/rex_cronjob_yform_auto_delete.sql');
        rex_sql::factory()->setQuery($query);
        $addon->setProperty('successmsg', '<br><strong>' . rex_i18n::msg('auto_delete.auto_install') . '</strong>');
    }
}
