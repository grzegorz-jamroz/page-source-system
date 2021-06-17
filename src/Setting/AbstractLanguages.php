<?php
declare(strict_types=1);

namespace PageSourceSystem\Setting;

use Ifrost\PageSourceComponents\AbstractSetting;
use PlainDataTransformer\Transform;

class AbstractLanguages extends AbstractSetting
{
    /**
     * @param array<int, string> $supportedLanguages
     */
    public function __construct(
        private string $defaultLanguage,
        private array $supportedLanguages,
    ) {
    }

    public static function getTypename(): string
    {
        return "Languages";
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
            "__typename" => self::getTypename(),
            "defaultLanguage" => $this->defaultLanguage,
            "supportedLanguages" => $this->supportedLanguages,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            Transform::toString($data['defaultLanguage'] ??= ""),
            Transform::toArray($data['supportedLanguages'] ??= []),
        );
    }
}
