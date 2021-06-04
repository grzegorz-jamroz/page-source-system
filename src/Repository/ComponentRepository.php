<?php

declare(strict_types=1);

namespace PageSourceSystem\Repository;

use Ifrost\PageSourceComponents\ComponentCollection;
use Ifrost\PageSourceComponents\ComponentInterface;
use PageSourceSystem\Storage\ComponentStorage;
use PlainDataTransformer\Transform;
use Ramsey\Uuid\Uuid;

class ComponentRepository
{
    public function __construct(
        private SettingsRepository $settings,
        private ComponentCollection $components
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function makeComponent(string $typename, array $data = []): ComponentInterface
    {
        $component = $this->components->get($typename);

        if (null === $component) {
            throw new \Exception(sprintf('Component with typename "%s" not exists.', $typename));
        }

        return $component::createFromArray($data);
    }

    public function getComponent(string $uuid): ComponentInterface
    {
        $data = $this->getComponentData($uuid);
        $typename = Transform::toString($data['__typename'] ??= '');

        return $this->makeComponent($typename, $data);
    }

    /**
     * @return array<string, mixed>
     */
    public function getComponentData(string $uuid): array
    {
        return $this->getComponentStorage($uuid)->getData();
    }

    public function deleteComponent(string $uuid): bool
    {
        return $this->getComponentStorage($uuid)->delete();
    }

    /**
     * @return array<string, array>
     */
    public function getComponentsData(): array
    {
        $uuids = $this->getComponentUuids();
        $output = [];

        foreach ($uuids as $uuid) {
            $output[$uuid] = $this->getComponentData($uuid);
        }

        return $output;
    }

    /**
     * @return array<int, string>
     */
    public function getComponentUuids(): array
    {
        $results = [];

        foreach ($this->settings->getLanguages() as $language) {
            $directory = ComponentStorage::getDirectory(
                $this->settings->getDirectory(),
                $language
            );
            $results = array_merge(
                $results,
                array_diff(scandir($directory) ?: [], ['..', '.'])
            );
        }

        $results = array_map(fn ($result) => str_replace('.json', '', $result), $results);
        $results = array_filter(
            $results,
            fn ($result) => Uuid::isValid($result)
        );

        return array_values($results);
    }

    private function getComponentStorage(string $uuid): ComponentStorage
    {
        foreach ($this->settings->getLanguages() as $language) {
            $storage = new ComponentStorage(
                $this->settings->getDirectory(),
                $language,
                $uuid
            );

            try {
                $storage->getData();

                return $storage;
            } catch (\Exception) {
            }
        }

        throw new \Exception(sprintf('Component %s not exists.', $uuid));
    }
}
