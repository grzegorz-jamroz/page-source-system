<?php

declare(strict_types=1);

namespace PageSourceSystem\Domain;

use PlainDataTransformer\Transform;

class ComponentInfo
{
    /**
     * @param array<int, string> $updateCommandClasses
     */
    public function __construct(
        private string $htmlClass,
        private array $updateCommandClasses,
        private string $formSerializerClass,
        private string $type,
    ) {
    }

    public function getHtmlClass(): string
    {
        return $this->htmlClass;
    }

    /**
     * @return array<int, string> $updateCommandClasses
     */
    public function getUpdateCommandClasses(): array
    {
        return $this->updateCommandClasses;
    }

    public function getFormSerializerClass(): string
    {
        return $this->formSerializerClass;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            Transform::toString($data['htmlClass'] ??= ''),
            Transform::toArray($data['updateCommandClasses'] ??= []),
            Transform::toString($data['formSerializerClass'] ??= ''),
            Transform::toString($data['type'] ??= ''),
        );
    }
}
