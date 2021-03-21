<?php

declare(strict_types=1);

namespace PageSourceSystem\Storage;

use SimpleStorageSystem\Storage\AbstractJsonData;
use SimpleStorageSystem\Utilities\Explorer;

class ComponentStorage extends AbstractJsonData
{
    public function __construct(
        private string $directory,
        private string $language,
        private string $uuid,
    ) {
        parent::__construct($this->getFilename());
    }

    private function getFilename(): string
    {
        $directory = sprintf('%s/%s/component', $this->directory, $this->language);
        Explorer::createDirectoryIfNotExists($directory);

        return sprintf('%s/%s.json', $directory, $this->uuid);
    }
}
