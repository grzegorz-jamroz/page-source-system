<?php

declare(strict_types=1);

namespace PageSourceSystem\Repository;

use PageSourceSystem\Storage\UserStorage;
use PageSourceSystem\User\AbstractUser;
use PageSourceSystem\User\UserFactoryInterface;
use Ramsey\Uuid\Uuid;

class UserRepository
{
    public function __construct(
        private string $directory,
        private UserFactoryInterface $userFactory,
    ) {
    }

    public function getUser(string $uuid): AbstractUser
    {
        try {
            $data = $this->getUserStorage($uuid)->getData();
        } catch (\Exception $e) {
            throw new \Exception(sprintf('User with uuid "%s" not exists.', $uuid));
        }

        return $this->userFactory->create($data);
    }

    /**
     * @return array<string, AbstractUser>
     */
    public function getUsers(): array
    {
        $uuids = $this->getUserUuids();
        $output = [];

        foreach ($uuids as $uuid) {
            $output[$uuid] = $this->getUser($uuid);
        }

        return $output;
    }

    /**
     * @return array<int, string>
     */
    public function getUserUuids(): array
    {
        $results = [];
        $directory = UserStorage::getDirectory($this->directory);
        $results = array_merge(
            $results,
            array_diff(scandir($directory) ?: [], ['..', '.'])
        );
        $results = array_map(fn ($result) => str_replace('.json', '', $result), $results);
        $results = array_filter(
            $results,
            fn ($result) => Uuid::isValid($result)
        );

        return array_values($results);
    }

    protected function getUserStorage(string $uuid): UserStorage
    {
        $storage = new UserStorage(
            $this->directory,
            $uuid
        );

        try {
            $storage->getData();

            return $storage;
        } catch (\Exception) {
        }

        throw new \Exception(sprintf('User %s not exists.', $uuid));
    }
}
