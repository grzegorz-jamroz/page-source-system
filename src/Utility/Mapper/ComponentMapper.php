<?php

declare(strict_types=1);

namespace PageSourceSystem\Utility\Mapper;

use Ifrost\Common\ArrayMapper;
use Ifrost\PageSourceComponents\AbstractComponent;
use PageSourceSystem\Repository\ComponentRepository;
use PlainDataTransformer\Transform;

class ComponentMapper
{
    public function __construct(private ComponentRepository $componentRepository)
    {
    }

    public function getNestedComponents(mixed $property): mixed
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
     * @param array<string, mixed> $component
     *
     * @return array<string, mixed>
     */
    public static function getWithFieldsAllowedForRender(array $component): array
    {
        return array_filter(
            $component,
            fn ($value, $key) => !in_array($key, AbstractComponent::FIELDS_NOT_FOR_RENDER),
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * @param array<mixed, array> $components
     * @param array<int, string>  $relations
     *
     * @return array<int, array>
     */
    public static function getOptions(
        array $components,
        array $relations = [],
    ): array {
        $components = ArrayMapper::orderBy($components, '__typename');

        if ([] === $relations) {
            return static::getAllComponentOptions($components);
        }

        return static::getOptionsForRelations($components, $relations);
    }

    /**
     * @param array<mixed, array> $components
     *
     * @return array<int, array>
     */
    private static function getAllComponentOptions(array $components): array
    {
        $output = [];

        foreach ($components as $component) {
            if ('Seo' === $component['__typename']) {
                continue;
            }

            $output[] = static::getOptionsOutput($component);
        }

        return $output;
    }

    /**
     * @param array<mixed, array> $components
     * @param array<int, string>  $relations
     *
     * @return array<int, array>
     */
    private static function getOptionsForRelations(
        array $components,
        array $relations,
    ): array {
        $output = [];

        foreach ($relations as $relation) {
            foreach ($components as $component) {
                if (!in_array($relation, $component['relations'] ?? [])) {
                    continue;
                }

                $output[] = static::getOptionsOutput($component);
            }
        }

        return $output;
    }

    /**
     * @param array<string, string> $component
     *
     * @return array<string, string>
     */
    private static function getOptionsOutput(array $component): array
    {
        $internalTitle = $component['internalTitle'] ?? '';
        $uuid = $component['uuid'] ?? '';
        $typename = $component['__typename'] ?? '';
        $label = $internalTitle;

        if ('' === $label) {
            $label = $uuid;
        }

        return [
            'value' => $uuid,
            'label' => $label,
            '__typename' => $typename,
            'internalTitle' => $internalTitle,
            'labelName' => $component['label'] ?? '',
        ];
    }
}
