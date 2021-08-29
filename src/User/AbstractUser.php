<?php

declare(strict_types=1);

namespace PageSourceSystem\User;

use Ifrost\Common\ArrayConstructable;

abstract class AbstractUser implements ArrayConstructable, \JsonSerializable
{
    /**
     * @param array<int, string> $roles
     */
    public function __construct(
        protected string $uuid,
        protected string $username,
        protected string $email,
        protected array $roles,
        protected string $password,
        protected string $apiKey,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'uuid' => $this->uuid,
            'username' => $this->username,
            'email' => $this->email,
            'roles' => $this->roles,
            'password' => $this->password,
            'apiKey' => $this->apiKey,
        ];
    }

    /**
     * @return array<int, string>
     */
    public function getFields(): array
    {
        return array_keys($this->jsonSerialize());
    }
}
