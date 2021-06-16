<?php
declare(strict_types=1);

namespace PageSourceSystem\Domain;

use SimpleStorageSystem\Utilities\Explorer;

class SettingFilename implements \Stringable
{
    public function __construct(
        private string $setting,
        private string $directory
    ) {
    }

    public function __toString(): string
    {
        $directory = sprintf('%s/settings', $this->directory);
        Explorer::createDirectoryIfNotExists($directory);

        return sprintf('%s/%s.json', $directory, $this->setting);
    }
}
