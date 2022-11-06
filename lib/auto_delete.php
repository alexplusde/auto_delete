<?php

class auto_delete
{
    public static function yform_auto_delete()
    {
        foreach (rex_sql::factory()->getArray('SELECT  * FROM `' . rex::getTable('yform_field') . '` WHERE `type_name` = "datestamp_auto_delete" ') as $field) {
            rex_sql::factory()->setQuery('DELETE FROM '.$field['table_name'].' WHERE '.$field['name'] .' < NOW()');
        }
    }

    
    public static function writeCronjob()
    {
        $cronjobs = rex_sql::factory()->setDebug(0)->getArray("SELECT * FROM rex_cronjob WHERE `type` LIKE '%_auto_delete'");

        foreach ($cronjobs as $cronjob) {
            rex_file::put(rex_path::addon("auto_delete", "cronjob/".$cronjob['type'].".json"), json_encode($cronjob));
        }
    }
    public static function updateCronjob()
    {
        $cronjobs = scandir(rex_path::addon('auto_delete').'cronjob');

        foreach ($cronjobs as $cronjob) {
            if ('.' == $cronjob || '..' == $cronjob) {
                continue;
            }
            $cronjob_array = json_decode(rex_file::get(rex_path::addon('auto_delete').'cronjob/'.$cronjob), 1);

            rex_sql::factory()->setDebug(0)->setTable('rex_cronjob')
           ->setTable(rex::getTable('cronjob'))
           ->setValue('name', $cronjob_array['name'])
           ->setValue('description', $cronjob_array['description'])
           ->setValue('type', $cronjob_array['type'])
           ->setValue('interval', $cronjob_array['interval'])
           ->setValue('environment', $cronjob_array['environment'])
           ->setValue('execution_start', '1970-01-01 01:00:00')
           ->setValue('status', '1')
           ->setValue('parameters', $cronjob_array['parameters'])
           ->setValue('nexttime', $cronjob_array['nexttime'])
           ->setValue('createdate', '')
           ->setValue('updatedate', '')
           ->setValue('createuser', 'auto_delete')
           ->setValue('updateuser', 'auto_delete')
    ->insertOrUpdate();
        }
    }
}
