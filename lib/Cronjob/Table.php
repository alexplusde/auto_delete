<?php

namespace Alexplusde\AutoDelete\Cronjob;

class Table extends rex_cronjob
{
    public function execute()
    {
        $sql = rex_sql::factory();

        // Parameter aus den Einstellungen holen
        $table = $this->getParam('rex_table');
        $field = $this->getParam('field');
        $interval = (int) $this->getParam('interval');

        // Prüfen, ob die Tabelle existiert
        $checkTableQuery = 'SHOW TABLES LIKE ' . $sql->escape($table);
        $sql->setQuery($checkTableQuery);

        if (0 === $sql->getRows()) {
            $this->setMessage('Fehler: Tabelle "' . $table . '" existiert nicht.');
            return false;
        }

        // Prüfen, ob das Feld in der Tabelle existiert
        $checkFieldQuery = sprintf('SHOW COLUMNS FROM `%s` LIKE %s',
            $table, // Tabellenname als Identifier (mit Backticks)
            $sql->escape($field), // Feldname als String (escaped)
        );
        $sql->setQuery($checkFieldQuery);

        if (0 === $sql->getRows()) {
            $this->setMessage('Fehler: Feld "' . $field . '" existiert nicht in Tabelle "' . $table . '".');
            return false;
        }

        // Sichere DELETE-Query ausführen
        $deleteQuery = sprintf(
            'DELETE FROM `%s` WHERE `%s` < DATE_SUB(NOW(), INTERVAL %d MONTH)',
            $table, // Tabellenname als Identifier
            $field, // Feldname als Identifier
            $interval, // Integer ist bereits sicher
        );

        $sql->setQuery($deleteQuery);

        $this->setMessage('Datensätze in der Tabelle ' . $table . ' gelöscht, die älter als ' . $interval . ' Monate waren.');
        return true;
    }

    public function getTypeName()
    {
        return rex_i18n::msg('auto_delete_table');
    }

    public function getParamFields()
    {
        // Eingabefelder des Cronjobs definieren
        $fields = [
            [
                'label' => rex_i18n::msg('auto_delete_table_cronjob_rex_table_label'),
                'name' => 'rex_table',
                'type' => 'select',
                'options' => array_column(rex_sql::factory()->getArray('SELECT TABLE_NAME as id, TABLE_NAME as name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE "rex_%"'), 'id', 'name'),
                'notice' => rex_i18n::msg('auto_delete_table_cronjob_rex_table_notice'),
            ],
            [
                'label' => rex_i18n::msg('auto_delete_table_cronjob_field_label'),
                'name' => 'field',
                'type' => 'text',
                'default' => 'createdate',
                'notice' => rex_i18n::msg('auto_delete_table_cronjob_field_notice'),
            ],
            [
                'label' => rex_i18n::msg('auto_delete_table_cronjob_interval_label'),
                'name' => 'interval',
                'default' => '6',
                'type' => 'text',
                'notice' => rex_i18n::msg('auto_delete_table_cronjob_interval_notice'),
            ],
        ];

        return $fields;
    }
}
