<?php

namespace Alexplusde\AutoDelete\Cronjob;

use rex_cronjob;
use rex_i18n;
use rex_sql;

use function is_string;

class Table extends rex_cronjob
{
    public function execute(): bool
    {
        $sql = rex_sql::factory();

        // Parameter aus den Einstellungen holen und validieren
        $tableParam = $this->getParam('rex_table');
        $fieldParam = $this->getParam('field');
        $intervalParam = $this->getParam('interval');

        if (!is_string($tableParam) || '' === $tableParam) {
            $this->setMessage('Fehler: Ungültiger Tabellenname.');
            return false;
        }

        if (!is_string($fieldParam) || '' === $fieldParam) {
            $this->setMessage('Fehler: Ungültiger Feldname.');
            return false;
        }

        $table = $tableParam;
        $field = $fieldParam;
        $interval = is_numeric($intervalParam) ? (int) $intervalParam : 0;

        if ($interval <= 0) {
            $this->setMessage('Fehler: Ungültiges Intervall.');
            return false;
        }

        // Prüfen, ob die Tabelle existiert
        $checkTableQuery = 'SHOW TABLES LIKE ' . $sql->escape($table);
        $sql->setQuery($checkTableQuery);

        if (0 === $sql->getRows()) {
            $this->setMessage('Fehler: Tabelle "' . $table . '" existiert nicht.');
            return false;
        }

        // Prüfen, ob das Feld in der Tabelle existiert
        $sql->setQuery('SHOW COLUMNS FROM ' . $sql->escapeIdentifier($table) . ' LIKE ' . $sql->escape($field));

        if (0 === $sql->getRows()) {
            $this->setMessage('Fehler: Feld "' . $field . '" existiert nicht in Tabelle "' . $table . '".');
            return false;
        }

        // Sichere DELETE-Query ausführen
        $sql->setQuery('DELETE FROM ' . $sql->escapeIdentifier($table) . ' WHERE ' . $sql->escapeIdentifier($field) . ' < DATE_SUB(NOW(), INTERVAL ' . $interval . ' MONTH)');

        $this->setMessage('Datensätze in der Tabelle ' . $table . ' gelöscht, die älter als ' . $interval . ' Monate waren.');
        return true;
    }

    public function getTypeName(): string
    {
        return rex_i18n::msg('auto_delete.table');
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getParamFields(): array
    {
        // Eingabefelder des Cronjobs definieren
        $fields = [
            [
                'label' => rex_i18n::msg('auto_delete.table_cronjob_rex_table_label'),
                'name' => 'rex_table',
                'type' => 'select',
                'options' => array_column(
                    rex_sql::factory()->getArray(
                        'SELECT TABLE_NAME as id, TABLE_NAME as name
                        FROM INFORMATION_SCHEMA.TABLES
                        WHERE TABLE_NAME LIKE "rex_%"
                        AND TABLE_TYPE = "BASE TABLE"
                        AND TABLE_SCHEMA = DATABASE()',
                    ),
                    'id',
                    'name',
                ),
                'notice' => rex_i18n::msg('auto_delete.table_cronjob_rex_table_notice'),
            ],
            [
                'label' => rex_i18n::msg('auto_delete.table_cronjob_field_label'),
                'name' => 'field',
                'type' => 'text',
                'default' => 'createdate',
                'notice' => rex_i18n::msg('auto_delete.table_cronjob_field_notice'),
            ],
            [
                'label' => rex_i18n::msg('auto_delete.table_cronjob_interval_label'),
                'name' => 'interval',
                'default' => '6',
                'type' => 'text',
                'notice' => rex_i18n::msg('auto_delete.table_cronjob_interval_notice'),
            ],
        ];

        return $fields;
    }
}
