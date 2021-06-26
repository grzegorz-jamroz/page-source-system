<?php

declare(strict_types=1);

namespace PageSourceSystem\Generator;

use PageSourceSystem\Domain\Page;
use PageSourceSystem\Repository\ComponentRepository;
use PageSourceSystem\Storage\PageJsonStorage;
use PageSourceSystem\Utility\Mapper\ComponentMapper;
use PageSourceSystem\Utility\PageSeoDataTransformer;
use PlainDataTransformer\Transform;

class PageJsonGenerator implements GeneratorInterface, \JsonSerializable
{
    public function __construct(
        private Page $page,
        private ComponentRepository $componentRepository,
        private PageSeoDataTransformer $pageSeoDataTransformer,
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
        $seoData = $this->componentRepository->getComponentData(
            $this->page->getSeoUuid()
        );

        return $this->pageSeoDataTransformer->getCombinedWithPrimarySeo($seoData);
    }

    /**
     * @return array<int, array>
     */
    private function getComponents(): array
    {
        $components = [];

        foreach ($this->page->getComponents() as $component) {
            $uuid = (string) $component['uuid'] ?? '';
            $componentData = $this->componentRepository->getComponentData($uuid);
            $mappedComponent = ComponentMapper::getWithFieldsAllowedForRender($componentData);
            $components[] = $this->getNestedComponents($mappedComponent);
        }

        return $components;
    }

    /**
     * @return array<int, array>
     */
    private function getNestedComponents(mixed $property): mixed
    {
        if (!is_array($property)) {
            return $property;
        }

        return array_map(function(mixed $items) {
            $uuid = Transform::toString($items['uuid'] ?? '');

            if ($uuid !== '') {
                $componentData = $this->componentRepository->getComponentData($uuid);

                return ComponentMapper::getWithFieldsAllowedForRender($componentData);
            }

            return $this->getNestedComponents($items);
        }, $property);
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
