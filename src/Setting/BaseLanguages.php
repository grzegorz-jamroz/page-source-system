<?php

declare(strict_types=1);

namespace PageSourceSystem\Setting;

use PlainDataTransformer\Transform;

class BaseLanguages extends AbstractLanguages
{
    /**
     * @param array<string, mixed> $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            Transform::toString($data['defaultLanguage'] ??= ''),
            Transform::toArray($data['supportedLanguages'] ??= []),
            Transform::toArray($data['supportedLanguagesOptions'] ??= []),
        );
    }
}
