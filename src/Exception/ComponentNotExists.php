<?php

declare(strict_types=1);

namespace PageSourceSystem\Exception;

use Throwable;

class ComponentNotExists extends \Exception
{
    public function __construct(string $typename, int $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Component with typename "%s" not exists.', $typename);
        parent::__construct($message, $code, $previous);
    }
}
