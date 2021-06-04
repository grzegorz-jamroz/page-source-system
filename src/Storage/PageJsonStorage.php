<?php

declare(strict_types=1);

namespace PageSourceSystem\Storage;

use SimpleStorageSystem\Storage\AbstractJsonData;
use SimpleStorageSystem\Utilities\Explorer;

class PageJsonStorage extends AbstractJsonData
{
    public function __construct(
        private string $directory,
        private string $language,
        private string $fileName,
    ) {
        parent::__construct($this->getFilename());
    }

    private function getFilename(): string
    {
        $directory = sprintf('%s/%s/json', $this->directory, $this->language);
        Explorer::createDirectoryIfNotExists($directory);

        return sprintf(
            '%s/%s.json',
            $directory,
            $this->fileName
        );
    }
}
