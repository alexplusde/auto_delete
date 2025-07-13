<?php

namespace Alexplusde\AutoDelete;

use rex;
use rex_addon;
use rex_cronjob_manager;

if (rex_addon::get('cronjob')->isAvailable() && !rex::isSafeMode()) {
    rex_cronjob_manager::registerType(Table::class);
    rex_cronjob_manager::registerType(YFormTable::class);
    rex_cronjob_manager::registerType(Folder::class);
}

// auto_delete::writeCronjob();
