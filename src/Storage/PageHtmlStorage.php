<?php

declare(strict_types=1);

namespace PageSourceSystem\Storage;

use SimpleStorageSystem\Storage\HtmlData;
use SimpleStorageSystem\Utilities\Explorer;

class PageHtmlStorage extends HtmlData
{
    public function __construct(
        private string $directory,
        private string $language,
        private string $fileName
    ) {
        parent::__construct($this->getFilename());
    }

    private function getFilename(): string
    {
        $directory = sprintf('%s/%s', $this->directory, $this->language);
        Explorer::createDirectoryIfNotExists($directory);

        return sprintf('%s/%s.html', $directory, $this->fileName);
    }
}
