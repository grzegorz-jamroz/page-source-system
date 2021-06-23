<?php

declare(strict_types=1);

namespace PageSourceSystem\Setting;

use PlainDataTransformer\Transform;

class BaseLanguages extends AbstractLanguages
{
    /**
     * @param array<int, string> $supportedLanguages
     */
    public function __construct(
        private string $defaultLanguage,
        private array $supportedLanguages,
    ) {
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
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            Transform::toString($data['defaultLanguage'] ??= ''),
            Transform::toArray($data['supportedLanguages'] ??= []),
        );
    }
}
