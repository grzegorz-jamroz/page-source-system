<?php

declare(strict_types=1);

namespace PageSourceSystem\Setting;

use Ifrost\PageSourceComponents\AbstractSetting;

abstract class AbstractGeneral extends AbstractSetting
{
    final public static function getTypename(): string
    {
        return 'General';
    }

    abstract public function getPrimarySeoUuid(string $language): string;
}
