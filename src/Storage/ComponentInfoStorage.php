<?php

declare(strict_types=1);

namespace PageSourceSystem\Storage;

use Doctrine\Common\Collections\ArrayCollection;
use PageSourceSystem\Domain\ComponentInfo;
use PageSourceSystem\Exception\InvalidComponent;
use PlainDataTransformer\Transform;
use SimpleStorageSystem\Storage\AbstractJsonData;
use SimpleStorageSystem\Utilities\Explorer;

class ComponentInfoStorage extends AbstractJsonData
{
    private ArrayCollection $components;

    public function __construct(
        private string $directory,
    ) {
        parent::__construct($this->getFilename());
    }

    private function getFilename(): string
    {
        Explorer::createDirectoryIfNotExists($this->directory);

        return sprintf('%s/component-info.json', $this->directory);
    }

    public function getComponents(): ArrayCollection
    {
        if (!isset($this->components)) {
            $this->components = new ArrayCollection($this->read());
        }

        return $this->components;
    }

    public function getComponent(string $typename): ComponentInfo
    {
        $components = $this->getComponents();

        if (!$components->containsKey($typename)) {
            throw new \Exception(sprintf('Component %s not exists.', $typename));
        }

        return ComponentInfo::createFromArray($components->get($typename));
    }

    /**
     * @param array<string, mixed> $component
     */
    public function getComponentInfo(array $component): ComponentInfo
    {
        if ([] === $component) {
            throw new InvalidComponent('Selected component has no data.');
        }

        $typename = Transform::toString($component['__typename'] ??= '');

        return $this->getComponent($typename);
    }
}
