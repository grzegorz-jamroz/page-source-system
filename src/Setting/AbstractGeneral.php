<?php

declare(strict_types=1);

namespace PageSourceSystem\Setting;

use Ifrost\PageSourceComponents\AbstractSetting;

abstract class AbstractGeneral extends AbstractSetting
{
    /**
     * @param array<string, string> $primarySeo
     */
    public function __construct(
        protected array $primarySeo,
    ) {
    }

    final public static function getTypename(): string
    {
        return 'General';
    }

    public function getPrimarySeoUuid(string $language): string
    {
        return $this->primarySeo[$language] ??= "";
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            '__typename' => self::getTypename(),
            'primarySeo' => $this->primarySeo,
        ];
    }
}
