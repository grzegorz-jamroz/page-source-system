<?php

declare(strict_types=1);

namespace PageSourceSystem\Storage;

use Ifrost\PageSourceComponents\ComponentCollection;
use SimpleStorageSystem\Document\Exception\FileNotExists;
use SimpleStorageSystem\Storage\AbstractJsonData;
use SimpleStorageSystem\Utilities\Explorer;

class ComponentStorage extends AbstractJsonData
{
    private ComponentCollection $components;

    public function __construct(
        private string $directory,
        private string $language,
        private string $fileName,
    ) {
        parent::__construct($this->getFilename());
    }

    private function getFilename(): string
    {
        $directory = sprintf('%s/%s/component', $this->directory, $this->language);
        Explorer::createDirectoryIfNotExists($directory);

        return sprintf('%s/%s.json', $directory, $this->fileName);
    }

    /**
     * @return array<string, mixed>
     * @throws FileNotExists
     */
    public function getComponent(): array
    {
        return $this->reader->read();
    }
}
