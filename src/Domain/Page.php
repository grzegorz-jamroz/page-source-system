<?php

declare(strict_types=1);

namespace PageSourceSystem\Domain;

use HtmlCreator\ArrayConstructable;
use PlainDataTransformer\Transform;

class Page implements \JsonSerializable, ArrayConstructable
{
    /**
     * @param array<int, string> $components
     */
    public function __construct(
        private string $uuid,
        private string $language,
        private string $url,
        private string $header,
        private string $seoUuid,
        private array $components,
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function getSeoUuid(): string
    {
        return $this->seoUuid;
    }

    /**
     * @return array<int, string>
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            Transform::toString($data['uuid'] ??= ''),
            Transform::toString($data['language'] ??= ''),
            Transform::toString($data['url'] ??= ''),
            Transform::toString($data['header'] ??= ''),
            Transform::toString($data['seo']['uuid'] ??= ''),
            Transform::toArray($data['components'] ??= []),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'uuid' => $this->uuid,
            'language' => $this->language,
            'url' => $this->url,
            'header' => $this->header,
            'seo' => [
                'uuid' => $this->seoUuid,
            ],
            'components' => $this->components,
        ];
    }

    /**
     * @return array<int, string>
     */
    public function getFields(): array
    {
        return array_keys($this->jsonSerialize());
    }
}
