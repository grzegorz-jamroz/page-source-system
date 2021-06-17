<?php

declare(strict_types=1);

namespace PageSourceSystem\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Ifrost\PageSourceComponents\SettingCollection;
use Ifrost\PageSourceComponents\SettingInterface;
use PageSourceSystem\Setting\AbstractLanguages;
use PageSourceSystem\Storage\SettingsStorage;

class SettingsRepository
{
    const SETTING_PRIMARY_SEO = 'primary-seo';

    public function __construct(
        private string $directory,
        private SettingCollection $settings
    ) {
    }

    public function getDefaultLanguage(): string
    {
        /** @var AbstractLanguages $setting */
        $setting = $this->getSetting(AbstractLanguages::getTypename());

        return $setting->getDefaultLanguage();
    }

    /**
     * @return ArrayCollection<int, string>
     */
    public function getSupportedLanguages(): ArrayCollection
    {
        /** @var AbstractLanguages $setting */
        $setting = $this->getSetting(AbstractLanguages::getTypename());

        return new ArrayCollection($setting->getSupportedLanguages());
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

    public function getPrimarySeoUuid(string $language): string
    {
        return $this->getSettingData(self::SETTING_PRIMARY_SEO)[$language];
    }

    public function getSettingData(string $typename): mixed
    {
        return $this->getSettingStorage($typename)->getData();
    }

    public function deleteSetting(string $typename): bool
    {
        return $this->getSettingStorage($typename)->delete();
    }

    /**
     * @return array<string, array>
     */
    public function getSettingsData(): array
    {
        $output = [];

        foreach ($this->settings as $typename => $settingClass) {
            $output[$typename] = $this->getSettingData($typename);
        }

        return $output;
    }

    public function getSettingStorage(string $typename): SettingsStorage
    {
        return new SettingsStorage($typename, $this->directory);
    }
}
