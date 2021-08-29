<?php

declare(strict_types=1);

namespace PageSourceSystem\User;

class UserFactory implements UserFactoryInterface
{
    public function create(array $data): User
    {
        return User::createFromArray($data);
    }
}
