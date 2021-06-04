<?php
declare(strict_types=1);

namespace PageSourceSystem\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use PageSourceSystem\Domain\Page;
use PageSourceSystem\Storage\PageStorage;
use Ramsey\Uuid\Uuid;

class PageRepository
{
    public function __construct(private SettingsRepository $settings) {
    }

    public function getPage(string $uuid): Page
    {
        $data = $this->getPageData($uuid);

        return Page::createFromArray($data);
    }

    public function getPages(): ArrayCollection
    {
        $pagesData = $this->getPagesData();
        $pages = new ArrayCollection();

        foreach ($pagesData as $pageData) {
            $page = Page::createFromArray($pageData);
            $pages->set($page->getUuid(), $page);
        }

        return $pages;
    }

    /**
     * @return array<string, array>
     */
    public function getPagesData(): array
    {
        $uuids = $this->getPageUuids();
        $output = [];

        foreach ($uuids as $uuid) {
            $output[$uuid] = $this->getPageData($uuid);
        }

        return $output;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPageData(string $uuid): array
    {
        return $this->getPageStorage($uuid)->getPageData();
    }

    public function deletePage(string $uuid): bool
    {
        return $this->getPageStorage($uuid)->delete();
    }

    /**
     * @return array<int, string>
     */
    public function getPageUuids(): array
    {
        $results = [];

        foreach ($this->settings->getLanguages() as $language) {
            $directory = PageStorage::getDirectory(
                $this->settings->getDirectory(),
                $language
            );
            $results = array_merge(
                $results,
                array_diff(scandir($directory) ?: [], ['..', '.'])
            );
        }

        $results = array_map(fn($result) => str_replace('.json', '', $result), $results);
        $results = array_filter(
            $results,
            fn($result) => Uuid::isValid($result)
        );

        return array_values($results);
    }

    private function getPageStorage(string $uuid): PageStorage
    {
        foreach ($this->settings->getLanguages() as $language) {
            $storage = new PageStorage(
                $this->settings->getDirectory(),
                $language,
                $uuid
            );

            try {
                $storage->getPageData();

                return $storage;
            } catch (\Exception) {

            }
        }

        throw new \Exception(sprintf('Page %s not exists.', $uuid));
    }
}
