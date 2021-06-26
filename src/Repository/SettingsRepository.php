<?php

declare(strict_types=1);

namespace PageSourceSystem\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Ifrost\PageSourceComponents\SettingCollection;
use Ifrost\PageSourceComponents\SettingInterface;
use PageSourceSystem\Domain\Seo;
use PageSourceSystem\Exception\SettingNotExists;
use PageSourceSystem\Setting\AbstractGeneral;
use PageSourceSystem\Setting\AbstractLanguages;
use PageSourceSystem\Setting\BaseGeneral;
use PageSourceSystem\Setting\BaseLanguages;
use PageSourceSystem\Storage\SettingsStorage;
use Ramsey\Uuid\Uuid;
use SimpleStorageSystem\Document\Exception\FileNotExists;

class SettingsRepository
{
    public function __construct(
        private string $directory,
        private SettingCollection $settings,
    ) {
    }

    public function getDefaultLanguage(): string
    {
        $setting = $this->getLanguages();

        return $setting->getDefaultLanguage();
    }

    /**
     * @return ArrayCollection<int, string>
     */
    public function getSupportedLanguages(): ArrayCollection
    {
        $setting = $this->getLanguages();

        return new ArrayCollection($setting->getSupportedLanguages());
    }

    public function getPrimarySeoUuid(string $language): string
    {
        /** @var AbstractGeneral $setting */
        $setting = $this->getSetting(AbstractGeneral::getTypename());

        return $setting->getPrimarySeoUuid($language);
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
            throw new SettingNotExists(sprintf('Setting with typename "%s" not exists.', $typename));
        }

        return $setting::createFromArray($data);
    }

    /**
     * @throws FileNotExists
     * @throws SettingNotExists
     */
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
     * @throws FileNotExists
     */
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

    protected function generateDefaultLanguages(): void
    {
        $this->getSettingStorage(AbstractLanguages::getTypename())->overwrite(
            (new BaseLanguages('en', ['en']))->jsonSerialize()
        );
    }

    protected function generateDefaultGeneral(): void
    {
        $primarySeo = [];

        foreach ($this->getSupportedLanguages() as $language) {
            $uuid = (string) Uuid::uuid4();
            $primarySeo[$language] = $uuid;
            $seo = Seo::createFromArray([
                'uuid' => $uuid,
                'language' => $language,
            ]);
        }

        $this->getSettingStorage(AbstractGeneral::getTypename())->overwrite(
            (new BaseGeneral($primarySeo))->jsonSerialize()
        );
    }

    private function getLanguages(): AbstractLanguages
    {
        try {
            /** @var AbstractLanguages $setting */
            $setting = $this->getSetting(AbstractLanguages::getTypename());
        } catch (FileNotExists) {
            $this->generateDefaultLanguages();

            /** @var AbstractLanguages $setting */
            $setting = $this->getSetting(AbstractLanguages::getTypename());
        }

        return $setting;
    }
}
