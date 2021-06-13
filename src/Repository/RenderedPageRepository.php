<?php

declare(strict_types=1);

namespace PageSourceSystem\Repository;

use PageSourceSystem\Storage\PageHtmlStorage;
use PageSourceSystem\Storage\PageJsonStorage;

class RenderedPageRepository
{
    public function __construct(private SettingsRepository $settingsRepository)
    {
    }

    public function deletePage(string $uuid): bool
    {
        if ($this->getJsonPageStorage($uuid)->delete() && $this->getHtmlPageStorage($uuid)->delete()) {
            return true;
        }

        return false;
    }

    private function getJsonPageStorage(string $uuid): PageJsonStorage
    {
        foreach ($this->settingsRepository->getLanguages() as $language) {
            $storage = new PageJsonStorage(
                sprintf('%s/pages', $this->settingsRepository->getDirectory()),
                $language,
                $uuid
            );

            try {
                $storage->getData();

                return $storage;
            } catch (\Exception) {
            }
        }

        throw new \Exception(sprintf('Page %s not exists.', $uuid));
    }

    private function getHtmlPageStorage(string $uuid): PageHtmlStorage
    {
        foreach ($this->settingsRepository->getLanguages() as $language) {
            $storage = new PageHtmlStorage(
                sprintf('%s/pages', $this->settingsRepository->getDirectory()),
                $language,
                $uuid
            );

            try {
                $storage->getData();

                return $storage;
            } catch (\Exception) {
            }
        }

        throw new \Exception(sprintf('Page %s not exists.', $uuid));
    }
}
