<?php
declare(strict_types=1);

namespace PageSourceSystem\Setting;

use Ifrost\PageSourceComponents\SettingInterface;

interface LanguagesSetting extends SettingInterface
{
    public function getDefaultLanguage(): string;

    /**
     * @return array<int, string>
     */
    public function getSupportedLanguages(): array;
}
