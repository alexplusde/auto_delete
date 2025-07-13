<?php

namespace Alexplusde\AutoDelete;

use rex;
use rex_addon;
use rex_cronjob_manager;

if (rex_addon::get('cronjob')->isAvailable() && !rex::isSafeMode()) {
    rex_cronjob_manager::registerType(Cronjob\Table::class);
    rex_cronjob_manager::registerType(Cronjob\YFormTable::class);
    rex_cronjob_manager::registerType(Cronjob\Folder::class);
}

// auto_delete::writeCronjob();
