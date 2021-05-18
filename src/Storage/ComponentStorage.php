<?php

declare(strict_types=1);

namespace PageSourceSystem\Storage;

use Ifrost\PageSourceComponents\ComponentCollection;
use Ifrost\PageSourceComponents\ComponentInterface;
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

    public function getComponents(): ComponentCollection
    {
        if (!isset($this->components)) {
            $this->components = new ComponentCollection($this->read());
        }

        return $this->components;
    }

    public function getComponent(string $typename): ComponentInterface
    {
        $components = $this->getComponents();

        if (!$components->containsKey($typename)) {
            throw new \Exception(sprintf('Component %s not exists.', $typename));
        }

        return $components->getComponent($typename);
    }
}
