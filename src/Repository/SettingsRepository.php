<?php

declare(strict_types=1);

namespace PageSourceSystem\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Ifrost\PageSourceComponents\SettingCollection;
use Ifrost\PageSourceComponents\SettingInterface;
use PageSourceSystem\Storage\SettingsStorage;

class SettingsRepository
{
    const SETTING_LANGUAGES = 'languages';
    const SETTING_PRIMARY_SEO = 'primary-seo';

    public function __construct(
        private string $directory,
        private SettingCollection $settings
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function makeSetting(
        string $typename,
        array $data = []
    ): SettingInterface {
        $setting = $this->settings->get($typename);

        if (null === $setting) {
            throw new \Exception(sprintf('Setting with typename "%s" not exists.', $typename));
        }

        return $setting::createFromArray($data);
    }

    public function getSetting(string $typename): SettingInterface
    {
        $data = $this->getSettingData($typename);

        return $this->makeSetting($typename, $data);
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
        return new ArrayCollection($this->getSettingData(self::SETTING_LANGUAGES));
    }

    public function getPrimarySeoUuid(string $language): string
    {
        return $this->getSettingData(self::SETTING_PRIMARY_SEO)[$language];
    }

    public function getSettingData(string $typename): mixed
    {
        return $this->getStorage($typename)->getData();
    }

    public function getStorage(string $typename): SettingsStorage
    {
        return new SettingsStorage($typename, $this->directory);
    }
}
