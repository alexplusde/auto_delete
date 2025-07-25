<?php

namespace Alexplusde\AutoDelete;

use rex_i18n;
use rex_yform_value_datestamp;
use rex_yform_value_datetime;

class rex_yform_value_datestamp_auto_delete extends rex_yform_value_datestamp
{
    public function getDescription(): string
    {
        return 'datestamp_auto_delete|name|label|Y-m-d H:i:s|offset|[0-always,1-only if empty,2-never]|offset';
    }

    public function preValidateAction(): void
    {
        parent::preValidateAction();
        $currentValue = $this->getValue();
        $offset = $this->getElement('offset');

        if (false !== $currentValue && null !== $currentValue) {
            $timestamp = strtotime($currentValue . ' ' . $offset);
            if (false !== $timestamp) {
                $value = date('Y-m-d h:i:s', $timestamp);
                $this->setValue($value);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefinitions(): array
    {
        return [
            'type' => 'value',
            'name' => 'datestamp_auto_delete',
            'values' => [
                'name' => ['type' => 'name',   'label' => rex_i18n::msg('yform_values_defaults_name')],
                'label' => ['type' => 'text',    'label' => rex_i18n::msg('yform_values_defaults_label')],
                'format' => ['type' => 'choice', 'label' => rex_i18n::msg('yform_values_datetime_format'), 'choices' => rex_yform_value_datetime::VALUE_DATETIME_FORMATS, 'default' => rex_yform_value_datetime::VALUE_DATETIME_DEFAULT_FORMAT],
                'no_db' => ['type' => 'no_db',   'label' => rex_i18n::msg('yform_values_defaults_table'),  'default' => 0],
                'only_empty' => ['type' => 'choice',  'label' => rex_i18n::msg('yform_values_datestamp_only_empty'), 'default' => '0', 'choices' => 'translate:yform_always=0,translate:yform_onlyifempty=1,translate:yform_never=2'],
                'offset' => ['type' => 'text',   'label' => rex_i18n::msg('yform_values_datestamp_auto_delete.offset'), 'notice' => rex_i18n::msg('yform_values_datestamp_auto_delete.offset_notice'),  'default' => '+6 months'],
            ],
            'description' => rex_i18n::msg('yform_values_datestamp_auto_delete.description'),
            'db_type' => ['datetime'],
            'multi_edit' => false,
        ];
    }
}
