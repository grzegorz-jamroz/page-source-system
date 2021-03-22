<?php

declare(strict_types=1);

namespace PageSourceSystem\Generator;

use PageSourceSystem\Domain\Page;
use PageSourceSystem\Generator\GeneratorInterface;
use PageSourceSystem\Storage\ComponentInfoStorage;
use PageSourceSystem\Storage\ComponentStorage;
use PageSourceSystem\Storage\PageJsonStorage;

class PageJsonGenerator implements GeneratorInterface, \JsonSerializable
{
    public function __construct(
        private Page $page,
        private ComponentInfoStorage $componentInfoStorage,
        private string $appDataDir,
        private string $appRenderDir,
    ) {
    }

    public function generate(): void
    {
        (new PageJsonStorage(
            $this->appRenderDir,
            $this->page->getLanguage(),
            $this->page->getUuid()
        ))->overwrite($this->jsonSerialize());
    }

    /**
     * @return array<string, mixed>
     */
    private function getSeo(): array
    {
        return $this->getComponentData(
            $this->page->getLanguage(),
            $this->page->getSeoUuid()
        );
    }

    /**
     * @return array<int, array>
     */
    private function getComponents(): array
    {
        $components = [];
        $language = $this->page->getLanguage();

        foreach ($this->page->getComponents() as $uuid) {
            $components[] = $this->getComponentData($language, $uuid);
        }

        return $components;
    }

    /**
     * @return array<string, mixed>
     */
    private function getComponentData(
        string $language,
        string $uuid
    ): array {
        return (new ComponentStorage(
            $this->appDataDir,
            $language,
            $uuid,
        ))->read();
    }

    /**
     * @return array<string, array>
     */
    public function jsonSerialize(): array
    {
        return [
            'components' => array_merge(
                [$this->getSeo()],
                $this->getComponents()
            ),
        ];
    }
}
