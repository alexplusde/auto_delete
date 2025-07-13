<?php

namespace Alexplusde\AutoDelete\Cronjob;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use rex_cronjob;
use rex_file;
use rex_i18n;
use rex_mailer;
use rex_path;

use function count;

class Folder extends rex_cronjob
{
    private function purgeDir(int $days = 31, string $dir = ''): int
    {
        $log = 0;
        $files = glob($dir . '/*');
        if ($files) {
            foreach ($files as $file) {
                if (is_dir($file)) {
                    $log += self::purgeDir($days, $file);
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
                $this->setMessage('Files deleted: ' . $purgeLog);
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
        return rex_i18n::msg('auto_delete.folder');
    }

    public function getParamFields()
    {
        $folders = [];

        foreach ($iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                rex_path::data(),
                RecursiveDirectoryIterator::SKIP_DOTS,
            ),
            RecursiveIteratorIterator::SELF_FIRST,
        ) as $item) {
            // Note SELF_FIRST, so array keys are in place before values are pushed.

            if ($item instanceof RecursiveDirectoryIterator) {
                $subPath = $item->getSubPathName();
                if ($item->isDir()) {
                    // Create a new array key of the current directory name.
                    $folders[rex_path::data() . $subPath] = rex_path::data() . $subPath;
                }
            }
        }

        foreach ($iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                rex_path::cache(),
                RecursiveDirectoryIterator::SKIP_DOTS,
            ),
            RecursiveIteratorIterator::SELF_FIRST,
        ) as $item) {
            // Note SELF_FIRST, so array keys are in place before values are pushed.

            if ($item instanceof RecursiveDirectoryIterator) {
                $subPath = $item->getSubPathName();
                if ($item->isDir()) {
                    // Create a new array key of the current directory name.
                    $folders[rex_path::cache() . $subPath] = rex_path::cache() . $subPath;
                }
            }
        }

        return [
            [
                'label' => rex_i18n::msg('auto_delete.folder_days_label'),
                'name' => 'days',
                'type' => 'select',
                'options' => [
                    7 => '7 ' . rex_i18n::msg('auto_delete.folder_days'),
                    14 => '14 ' . rex_i18n::msg('auto_delete.folder_days'),
                    30 => '30 ' . rex_i18n::msg('auto_delete.folder_days'),
                    90 => '90 ' . rex_i18n::msg('auto_delete.folder_days'),
                    180 => '180 ' . rex_i18n::msg('auto_delete.folder_days'),
                ],
                'default' => 7,
            ], [
                'label' => rex_i18n::msg('auto_delete.folder_label'),
                'name' => 'dir',
                'type' => 'select',
                'attributes' => ['class' => 'form-control selectpicker'],
                'options' => $folders,
                'default' => 7,
                'notice' => rex_path::data(),
            ],
        ];
    }
}
