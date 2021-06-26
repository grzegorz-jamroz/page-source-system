<?php

declare(strict_types=1);

namespace PageSourceSystem\Setting;

use PlainDataTransformer\Transform;

class BaseGeneral extends AbstractGeneral
{
    /**
     * @param array<string, mixed> $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            Transform::toArray($data['primarySeo'] ??= []),
        );
    }
}
