<?php

declare(strict_types=1);

namespace PageSourceSystem\Storage;

use PageSourceSystem\Domain\Page;
use SimpleStorageSystem\Storage\AbstractJsonData;
use SimpleStorageSystem\Utilities\Explorer;

class PageStorage extends AbstractJsonData
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
        return sprintf(
            '%s/%s.json',
            $this->getDirectory(),
            $this->uuid
        );
    }

    /**
     * @return array|Page[]
     */
    public function getAllPages(): array
    {
        $directory = $this->getDirectory();
        $files = array_diff(scandir($directory) ?: [], ['..', '.']);
        $files = array_values($files);

        return array_map(function (string $file) {
            $uuid = str_replace('.json', '', $file);
            $storage = new PageStorage(
                $this->directory,
                $this->language,
                $uuid,
            );

            return Page::createFromArray($storage->read());
        }, $files);
    }

    private function getDirectory(): string
    {
        $directory = sprintf('%s/%s/page', $this->directory, $this->language);
        Explorer::createDirectoryIfNotExists($directory);

        return $directory;
    }
}
