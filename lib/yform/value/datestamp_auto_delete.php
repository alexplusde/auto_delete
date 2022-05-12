<?php

class rex_yform_value_datestamp_auto_delete extends rex_yform_value_abstract
{
    public function getDescription(): string
    {
        return 'datestamp_auto_delete|name|label|mysql|offset|[0-always,1-only if empty,2-never]';
    }

    
    public function preValidateAction(): void
    {
        $format = rex_sql::FORMAT_DATETIME;
        $default_value = date($format);
        $value = $this->getValue();
        $this->value_datestamp_currentValue = $value;
        if (2 == $this->getElement('only_empty')) {
            // wird nicht gesetzt
        } elseif (1 != $this->getElement('only_empty')) { // -> == 0
            // wird immer neu gesetzt
            $value = $default_value;
        } elseif ('' != $this->getValue() && '0000-00-00 00:00:00' != $this->getValue()) {
            // wenn Wert vorhanden ist direkt zurück
        } elseif (isset($this->params['sql_object']) && '' != $this->params['sql_object']->getValue($this->getName()) && '0000-00-00 00:00:00' != $this->params['sql_object']->getValue($this->getName())) {
            // sql object vorhanden und Wert gesetzt ?
        } else {
            $value = $default_value;
        }
        $value = date('Y-m-d h:i:s', strtotime($value. ' '.$this->getElement('offset')));

        $this->setValue($value);
    }


    public function getDefinitions(): array
    {
        return [
            'type' => 'value',
            'name' => 'datestamp_auto_delete',
            'values' => [
                'name' => ['type' => 'name',   'label' => rex_i18n::msg('yform_values_defaults_name')],
                'label' => ['type' => 'text',    'label' => rex_i18n::msg('yform_values_defaults_label')],
                'format' => ['type' => 'choice', 'label' => rex_i18n::msg('yform_values_datetime_format'), 'choices' => ['mysql'], 'default' => 'mysql'],
                'offset' => ['type' => 'text',   'label' => rex_i18n::msg('yform_values_datestamp_auto_delete_offset'),  'default' => '6 MONTH'],
                'only_empty' => ['type' => 'choice',  'label' => rex_i18n::msg('yform_values_datestamp_only_empty'), 'default' => '0', 'choices' => 'translate:yform_always=0,translate:yform_onlyifempty=1,translate:yform_never=2'],
            ],
            'description' => rex_i18n::msg('yform_values_datestamp_description'),
            'db_type' => ['datetime'],
            'multi_edit' => false,
        ];
    }
}
