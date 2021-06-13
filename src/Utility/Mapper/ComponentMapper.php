<?php

declare(strict_types=1);

namespace PageSourceSystem\Utility\Mapper;

use Ifrost\Common\ArrayMapper;
use Ifrost\PageSourceComponents\AbstractComponent;

class ComponentMapper
{
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
