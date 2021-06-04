<?php

declare(strict_types=1);

namespace PageSourceSystem\Storage;

use SimpleStorageSystem\Document\Exception\FileNotExists;
use SimpleStorageSystem\Storage\AbstractJsonData;
use SimpleStorageSystem\Utilities\Explorer;

class ComponentStorage extends AbstractJsonData
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
        return sprintf('%s/%s.json', $this->directory, $this->fileName);
    }

    /**
     * @return array<string, mixed>
     * @throws FileNotExists
     */
    public function getComponentData(): array
    {
        return $this->reader->read();
    }

    public static function getDirectory(
        string $directory,
        string $language
    ): string {
        $directory = sprintf('%s/%s/component', $directory, $language);
        Explorer::createDirectoryIfNotExists($directory);

        return $directory;
    }
}
