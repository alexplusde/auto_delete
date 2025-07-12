<?php

namespace Alexplusde\AutoDelete;

use rex_addon;
use rex_cronjob_manager;
use rex;

if (rex_addon::get('cronjob')->isAvailable() && !rex::isSafeMode()) {
    rex_cronjob_manager::registerType('auto_delete.table');
    rex_cronjob_manager::registerType('auto_delete.yform_table');
    rex_cronjob_manager::registerType('auto_delete.folder');
}

// auto_delete::writeCronjob();
