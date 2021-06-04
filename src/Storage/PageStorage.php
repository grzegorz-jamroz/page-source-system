<?php

declare(strict_types=1);

namespace PageSourceSystem\Storage;

use SimpleStorageSystem\Storage\AbstractJsonData;
use SimpleStorageSystem\Utilities\Explorer;

class PageStorage extends AbstractJsonData
{
    public function __construct(
        private string $directory,
        string $language,
        private string $fileName,
    ) {
        $this->directory = self::getDirectory($directory, $language);
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

    public static function getDirectory(
        string $directory,
        string $language
    ): string {
        $directory = sprintf('%s/%s/page', $directory, $language);
        Explorer::createDirectoryIfNotExists($directory);

        return $directory;
    }
}
