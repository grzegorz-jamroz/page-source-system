<?php

declare(strict_types=1);

namespace PageSourceSystem\Setting;

use Ifrost\PageSourceComponents\AbstractSetting;

abstract class AbstractLanguages extends AbstractSetting
{
    final public static function getTypename(): string
    {
        return 'Languages';
    }

    abstract public function getDefaultLanguage(): string;

    /**
     * @return array<int, string>
     */
    abstract public function getSupportedLanguages(): array;
}
