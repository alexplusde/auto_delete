<?php

namespace Alexplusde\AutoDelete\Cronjob;

use Alexplusde\AutoDelete\AutoDelete;
use rex_cronjob;
use rex_i18n;

class YFormTable extends rex_cronjob
{
    public function execute()
    {
        AutoDelete::YForm();
        return rex_i18n::msg('auto_delete.yform_cronjob_message');
    }

    public function getTypeName()
    {
        return rex_i18n::msg('auto_delete.yform');
    }

    public function getParamFields()
    {
    }
}
