<?php

declare(strict_types=1);

namespace PageSourceSystem\User;

interface UserFactoryInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): AbstractUser;
}
