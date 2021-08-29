<?php

declare(strict_types=1);

namespace PageSourceSystem\Storage;

use SimpleStorageSystem\Storage\AbstractJsonData;
use SimpleStorageSystem\Utilities\Explorer;

class UserStorage extends AbstractJsonData
{
    public function __construct(
        private string $directory,
        private string $fileName,
    ) {
        $this->directory = self::getDirectory($directory);
        parent::__construct($this->getFilename());
    }

    private function getFilename(): string
    {
        return sprintf(
            '%s/%s.json',
            $this->directory,
            $this->fileName
        );
    }

    public static function getDirectory(string $directory): string
    {
        $directory = sprintf('%s/user', $directory);
        Explorer::createDirectoryIfNotExists($directory);

        return $directory;
    }
}
