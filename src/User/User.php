<?php

declare(strict_types=1);

namespace PageSourceSystem\User;

use PlainDataTransformer\Transform;

class User extends AbstractUser
{
    public function __construct(
        protected string $uuid,
        protected string $username,
        protected string $email,
        protected array $roles,
        protected string $password,
        protected string $apiKey,
        protected string $resetPasswordToken,
        protected string $name,
        protected string $surname,
    ) {
        parent::__construct(
            $uuid,
            $username,
            $email,
            $roles,
            $password,
            $apiKey,
            $resetPasswordToken,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();

        return array_merge(
            $data,
            [
                'name' => $this->name,
                'surname' => $this->surname,
            ]
        );
    }

    /**
     * @return array<int, string>
     */
    public function getFields(): array
    {
        return array_keys($this->jsonSerialize());
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            Transform::toString($data['uuid'] ?? ''),
            Transform::toString($data['username'] ?? ''),
            Transform::toString($data['email'] ?? ''),
            Transform::toArray($data['roles'] ?? []),
            Transform::toString($data['password'] ?? ''),
            Transform::toString($data['apiKey'] ?? ''),
            Transform::toString($data['resetPasswordToken'] ?? ''),
            Transform::toString($data['name'] ?? ''),
            Transform::toString($data['surname'] ?? ''),
        );
    }
}
