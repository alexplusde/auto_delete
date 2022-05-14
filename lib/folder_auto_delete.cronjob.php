<?php

class rex_cronjob_folder_auto_delete extends rex_cronjob
{
    private function purgeDir(int $days = 31, string $dir = ''): int
    {
        $dir = rex_path::basename($dir);
        $log = 0;
        $files = glob($dir . '/*');
        if ($files) {
            foreach ($files as $file) {
                if (is_dir($file)) {
                    $log += self::purgeMailarchive($days, $file);
                } elseif ((time() - filemtime($file)) > (60 * 60 * 24 * $days)) {
                    if (rex_file::delete($file)) {
                        ++$log;
                    }
                }
            }
            if ('' != $dir && $dir != rex_mailer::logFolder() && is_dir($dir) && 0 === count(glob("$dir/*"))) {
                if (true == rmdir($dir)) {
                }
            }
        }
        return $log;
    }

    public function execute()
    {
        $dir = $this->getParam('dir');
        if ('' != $dir && is_dir($dir)) {
            $days = (int) $this->getParam('days');
            $purgeLog = self::purgeDir($days, $dir);
            if (0 != $purgeLog) {
                $this->setMessage('Files deleted: '.$purgeLog);
                return true;
            }
            $this->setMessage('No files found to delete');
            return true;
        }
        $this->setMessage('Unable to find folder');
        return false;
    }

    public function getTypeName()
    {
        return rex_i18n::msg('auto_delete_folder');
    }

    public function getParamFields()
    {
        return [
            [
                'label' => rex_i18n::msg('auto_delete_folder_days_label'),
                'name' => 'days',
                'type' => 'select',
                'options' => [
                    7 => '7 ' . rex_i18n::msg('auto_delete_folder_days'),
                    14 => '14 ' . rex_i18n::msg('auto_delete_folder_days'),
                    30 => '30 ' . rex_i18n::msg('auto_delete_folder_days'),
                    90 => '90 ' . rex_i18n::msg('auto_delete_folder_days'),
                    180 => '180 ' . rex_i18n::msg('auto_delete_folder_days'),
                ],
                'default' => 7,
            ],[
                'label' => rex_i18n::msg('auto_delete_folder_label'),
                'name' => 'dir',
                'type' => 'text',
                'default' => rex_path::coreCache(),
            ],
        ];
    }
}
