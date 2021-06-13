<?php

declare(strict_types=1);

namespace PageSourceSystem\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use PageSourceSystem\Storage\SettingsStorage;

class SettingsRepository
{
    const SETTING_LANGUAGES = 'languages';
    const SETTING_PRIMARY_SEO = 'primary-seo';

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
        return new ArrayCollection($this->getItemData(self::SETTING_LANGUAGES));
    }

    public function getPrimarySeoUuid(string $language): string
    {
        return $this->getItemData(self::SETTING_PRIMARY_SEO)[$language];
    }

    /**
     * @return array<mixed, mixed>
     */
    public function getItemSourceData(string $name): array
    {
        return $this->getStorage($name)->read();
    }

    public function getItemData(string $name): mixed
    {
        $data = $this->getItemSourceData($name);

        return $data['data'] ?? null;
    }

    public function getStorage(string $name): SettingsStorage
    {
        return new SettingsStorage($this->directory, $name);
    }
}
