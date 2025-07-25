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
use function is_array;
use function is_string;

class Folder extends rex_cronjob
{
    private function purgeDir(int $days = 31, string $dir = ''): int
    {
        $log = 0;
        $files = glob($dir . '/*');

        if (!is_array($files)) {
            return $log;
        }

        foreach ($files as $file) {
            if (is_dir($file)) {
                $log += $this->purgeDir($days, $file);
            } elseif ($this->isFileOlderThan($file, $days)) {
                if (rex_file::delete($file)) {
                    ++$log;
                }
            }
        }

        if ($this->shouldRemoveEmptyDir($dir)) {
            rmdir($dir);
        }

        return $log;
    }

    private function isFileOlderThan(string $file, int $days): bool
    {
        $fileTime = filemtime($file);
        if (false === $fileTime) {
            return false;
        }

        $fileAge = time() - $fileTime;
        return $fileAge > (60 * 60 * 24 * $days);
    }

    private function shouldRemoveEmptyDir(string $dir): bool
    {
        if ('' === $dir || $dir === rex_mailer::logFolder() || !is_dir($dir)) {
            return false;
        }

        $globResult = glob("$dir/*");
        return is_array($globResult) && 0 === count($globResult);
    }

    public function execute(): bool
    {
        $dirParam = $this->getParam('dir');
        $daysParam = $this->getParam('days');

        if (!is_string($dirParam) || '' === $dirParam) {
            $this->setMessage('Invalid directory parameter');
            return false;
        }

        if (!is_dir($dirParam)) {
            $this->setMessage('Unable to find folder');
            return false;
        }

        $days = is_numeric($daysParam) ? (int) $daysParam : 7;
        if ($days <= 0) {
            $days = 7; // Default fallback
        }

        $purgeLog = $this->purgeDir($days, $dirParam);
        if (0 !== $purgeLog) {
            $this->setMessage('Files deleted: ' . $purgeLog);
            return true;
        }
        $this->setMessage('No files found to delete');
        return true;
    }

    public function getTypeName(): string
    {
        return rex_i18n::msg('auto_delete.folder');
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getParamFields(): array
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
                $subPath = $item->getSubPathname();
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
                $subPath = $item->getSubPathname();
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
