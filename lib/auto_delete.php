<?php

class auto_delete
{
    public static function yform_auto_delete()
    {
        foreach (rex_sql::factory()->getArray('SELECT  * FROM `' . rex::getTable('yform_field') . '` WHERE `type_name` = "datestamp_auto_delete" ') as $field) {
            rex_sql::factory()->query('DELETE FROM '.$field['table_name'].' WHERE '.$field['name'] .' < NOW()');
        }
    }
}
