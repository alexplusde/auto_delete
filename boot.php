<?php

if (rex_addon::get('cronjob')->isAvailable() && !rex::isSafeMode()) {
    rex_cronjob_manager::registerType('rex_cronjob_table_auto_delete');
    rex_cronjob_manager::registerType('rex_cronjob_yform_auto_delete');
    rex_cronjob_manager::registerType('rex_cronjob_folder_auto_delete');
}
