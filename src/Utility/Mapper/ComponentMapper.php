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

    public function getFilteredNestedComponents(mixed $property): mixed
    {
        if (!is_array($property)) {
            return $property;
        }

        return array_map(function (mixed $items) {
            $uuid = Transform::toString($items['uuid'] ?? '');

            if ('' !== $uuid) {
                $componentData = $this->componentRepository->getComponentData($uuid);

                return ComponentMapper::getWithFieldsAllowedForRender($componentData);
            }

            return $this->getFilteredNestedComponents($items);
        }, $property);
    }

    public function getNestedComponents(mixed $property): mixed
    {
        if (!is_array($property)) {
            return $property;
        }

        return array_map(function (mixed $items) {
            $uuid = Transform::toString($items['uuid'] ?? '');

            if ('' !== $uuid) {
                $component = $this->componentRepository->getComponent($uuid);
                $componentData = $component->jsonSerialize();
                $componentData['htmlClass'] = $component->getHtmlClass();

                return $componentData;
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
     * @param array<mixed, array>  $components
     * @param array<string, array> $filters
     *
     * @return array<int, array>
     */
    public static function getOptions(
        array $components,
        array $filters,
    ): array {
        $relations = Transform::toArray($filters['relations'] ?? []);
        $typenames = Transform::toArray($filters['typenames'] ?? []);
        $components = ArrayMapper::orderBy($components, '__typename');

        if ([] === $relations && [] === $typenames) {
            return static::getAllComponentOptions($components);
        }

        return array_values(
            array_merge(
                static::getOptionsForRelations($components, $relations),
                static::getOptionsForTypenames($components, $typenames)
            )
        );
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
     * @return array<string, array>
     */
    private static function getOptionsForRelations(
        array $components,
        array $relations,
    ): array {
        $output = [];

        foreach ($relations as $relation) {
            foreach ($components as $component) {
                $componentRelations = Transform::toArray($component['relations'] ?? []);
                $uuid = Transform::toString($component['uuid'] ?? '');

                if ('' === $uuid || !in_array($relation, $componentRelations)) {
                    continue;
                }

                $output[$uuid] = static::getOptionsOutput($component);
            }
        }

        return $output;
    }

    /**
     * @param array<mixed, array> $components
     * @param array<int, string>  $typenames
     *
     * @return array<string, array>
     */
    private static function getOptionsForTypenames(
        array $components,
        array $typenames,
    ): array {
        $output = [];

        foreach ($typenames as $typename) {
            foreach ($components as $component) {
                $componentTypename = Transform::toString($component['__typename'] ?? '');
                $uuid = Transform::toString($component['uuid'] ?? '');

                if (
                    '' === $componentTypename
                    || '' === $uuid
                    || $typename !== $componentTypename
                ) {
                    continue;
                }

                $output[$uuid] = static::getOptionsOutput($component);
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
