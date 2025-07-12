<?php

namespace Alexplusde\AutoDelete\Cronjob;

class YFormTable extends rex_cronjob
{
    public function execute()
    {
        AutoDelete::yform_auto_delete();
        return rex_i18n::msg('auto_delete_yform_cronjob_message');
        return true;
    }

    public function getTypeName()
    {
        return rex_i18n::msg('auto_delete_yform');
    }

    public function getParamFields() {}
}
