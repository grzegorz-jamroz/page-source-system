<?php

declare(strict_types=1);

namespace PageSourceSystem\Exception;

use Throwable;

class AssetNotExists extends \Exception
{
    public function __construct(string $path, int $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Unable to find Asset under path "%s"', $path);
        parent::__construct($message, $code, $previous);
    }
}
