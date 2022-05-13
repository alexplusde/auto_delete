<?php

class rex_cronjob_yform_auto_delete extends rex_cronjob
{
    public function execute()
    {
        auto_delete::yform_auto_delete();
        return rex_i18n::msg('auto_delete_database_cronjob_message');
        return true;
    }

    public function getTypeName()
    {
        return rex_i18n::msg('auto_delete_yform');
    }

    public function getParamFields()
    {
    }
}
