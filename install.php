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

// Upgrade Cronjobs von v1 zu v2

// YForm-Tabellen
rex_sql::factory()
    ->setTable(rex::getTable('cronjob'))
    ->setWhere(['type' => 'rex_cronjob_yform_auto_delete'])
    ->setValue('type', 'Alexplusde\AutoDelete\Cronjob\YFormTable')
    ->update();

// SQL-Tabellen
rex_sql::factory()
    ->setTable(rex::getTable('cronjob'))
    ->setWhere(['type' => 'rex_cronjob_table_auto_delete'])
    ->setValue('type', 'Alexplusde\AutoDelete\Cronjob\Table')
    ->update();

// Folder
rex_sql::factory()
    ->setTable(rex::getTable('cronjob'))
    ->setWhere(['type' => 'rex_cronjob_folder_auto_delete'])
    ->setValue('type', 'Alexplusde\AutoDelete\Cronjob\Folder')
    ->update();
