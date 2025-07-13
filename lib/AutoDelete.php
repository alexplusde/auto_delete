<?php

namespace Alexplusde\AutoDelete;

use rex;
use rex_file;
use rex_path;
use rex_sql;
use rex_type;
use rex_yform_manager_query;

use function is_array;
use function is_string;

class AutoDelete
{
    public static function YForm(): void
    {
        $rex_sql = rex_sql::factory();

        foreach ($rex_sql->getArray('SELECT  * FROM `' . rex::getTable('yform_field') . '` WHERE `type_name` = "datestamp_auto_delete" ') as $field) {
            // Verwende YOrm statt rex_sql
            $collection = rex_yform_manager_query::get($field['table_name'])->where($field['name'], 'NOW()', '<')->find();
            $collection->delete();
        }
    }

    /** @api */
    public static function writeCronjob(): void
    {
        $cronjobs = rex_sql::factory()->setDebug(false)->getArray("SELECT * FROM rex_cronjob WHERE `type` LIKE '%AutoDelete%'");

        foreach ($cronjobs as $cronjob) {
            $content = json_encode($cronjob);
            if (false !== $content) {
                rex_file::put(rex_path::addon('auto_delete', 'cronjob/' . $cronjob['type'] . '.json'), $content);
            }
        }
    }

    /** @api */
    public static function updateCronjob(): void
    {
        $cronjobsDir = rex_path::addon('auto_delete') . 'cronjob';
        $cronjobs = scandir($cronjobsDir);

        if (!is_array($cronjobs)) {
            return;
        }

        foreach ($cronjobs as $cronjob) {
            if ('.' === $cronjob || '..' === $cronjob) {
                continue;
            }

            $cronjobContent = rex_file::get($cronjobsDir . '/' . $cronjob);
            if (!is_string($cronjobContent) || '' === $cronjobContent) {
                continue;
            }

            $cronjob_array = json_decode($cronjobContent, true);
            if (!is_array($cronjob_array)) {
                continue;
            }

            rex_sql::factory()->setDebug(false)->setTable(rex::getTable('cronjob'))
                ->setValue('name', rex_type::string($cronjob_array['name']))
                ->setValue('description', rex_type::string($cronjob_array['description']))
                ->setValue('type', rex_type::string($cronjob_array['type']))
                ->setValue('interval', rex_type::string($cronjob_array['interval']))
                ->setValue('environment', rex_type::string($cronjob_array['environment']))
                ->setValue('execution_start', '1970-01-01 01:00:00')
                ->setValue('status', '1')
                ->setValue('parameters', rex_type::string($cronjob_array['parameters']))
                ->setValue('nexttime', rex_type::string($cronjob_array['nexttime']))
                ->setValue('createdate', '')
                ->setValue('updatedate', '')
                ->setValue('createuser', 'auto_delete')
                ->setValue('updateuser', 'auto_delete')
                ->insertOrUpdate();
        }
    }
}
