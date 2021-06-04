<?php
declare(strict_types=1);

namespace PageSourceSystem\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use PageSourceSystem\Storage\SettingsStorage;
use SimpleStorageSystem\Document\Exception\FileNotExists;

class SettingsRepository
{
    public function __construct(private string $directory)
    {
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @return ArrayCollection<int, string>
     */
    public function getLanguages(): ArrayCollection
    {
        return new ArrayCollection($this->getStorage('languages')->read());
    }

    /**
     * @return array<mixed, mixed>
     * @throws FileNotExists
     */
    public function getItemData(string $name): array
    {
        return $this->getStorage($name)->read();
    }

    private function getStorage(string $name): SettingsStorage
    {
        return new SettingsStorage($this->directory, $name);
    }
}
