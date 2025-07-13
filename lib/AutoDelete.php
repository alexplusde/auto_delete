<?php

namespace Alexplusde\AutoDelete;

use rex;
use rex_file;
use rex_path;
use rex_sql;
use rex_yform_manager_query;

class AutoDelete
{
    public static function YForm(): void
    {
        $rex_sql = rex_sql::factory();

        foreach (rex_sql::factory()->getArray('SELECT  * FROM `' . rex::getTable('yform_field') . '` WHERE `type_name` = "datestamp_auto_delete" ') as $field) {
            // Verwende YOrm statt rex_sql
            $collection = rex_yform_manager_query::get($field['table_name'])->where($field['name'], 'NOW()', '<')->find();
            $collection->delete();
        }
    }

    public static function writeCronjob(): void
    {
        $cronjobs = rex_sql::factory()->setDebug(false)->getArray("SELECT * FROM rex_cronjob WHERE `type` LIKE '%AutoDelete%'");

        foreach ($cronjobs as $cronjob) {
            $content = json_encode($cronjob);
            if ($content !== false) {
                rex_file::put(rex_path::addon('auto_delete', 'cronjob/' . $cronjob['type'] . '.json'), $content);
            }
        }
    }

    public static function updateCronjob(): void
    {
        $cronjobs = scandir(rex_path::addon('auto_delete') . 'cronjob');

        foreach ($cronjobs as $cronjob) {
            if ('.' === $cronjob || '..' === $cronjob) {
                continue;
            }
            $cronjobContent = rex_file::get(rex_path::addon('auto_delete') . 'cronjob/' . $cronjob);
            if ($cronjobContent !== null && $cronjobContent !== false) {
                $cronjob_array = json_decode($cronjobContent, true);
                if ($cronjob_array !== null) {
                    rex_sql::factory()->setDebug(false)->setTable('rex_cronjob')
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
    }
}
