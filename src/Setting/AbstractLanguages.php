<?php

declare(strict_types=1);

namespace PageSourceSystem\Setting;

use Ifrost\PageSourceComponents\AbstractSetting;

abstract class AbstractLanguages extends AbstractSetting
{
    /**
     * @param array<int, string> $supportedLanguages
     * @param array<string, string> $supportedLanguagesOptions
     */
    public function __construct(
        protected string $defaultLanguage,
        protected array $supportedLanguages,
        protected array $supportedLanguagesOptions,
    ) {
    }

    final public static function getTypename(): string
    {
        return 'Languages';
    }

    public function getDefaultLanguage(): string
    {
        return $this->defaultLanguage;
    }

    /**
     * @return array<int, string>
     */
    public function getSupportedLanguages(): array
    {
        return $this->supportedLanguages;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            '__typename' => self::getTypename(),
            'defaultLanguage' => $this->defaultLanguage,
            'supportedLanguages' => $this->supportedLanguages,
            'supportedLanguagesOptions' => $this->supportedLanguagesOptions,
        ];
    }
}
