<?php
declare(strict_types=1);

namespace PageSourceSystem\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use PageSourceSystem\Storage\SettingsStorage;

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
        return new ArrayCollection($this->getSettingsStorage('languages')->read());
    }

    private function getSettingsStorage(string $name): SettingsStorage
    {
        return new SettingsStorage($this->directory, $name);
    }
}
