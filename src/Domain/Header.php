<?php

declare(strict_types=1);

namespace PageSourceSystem\Domain;

use HtmlCreator\ArrayConstructable;
use PlainDataTransformer\Transform;

class Header implements \JsonSerializable, ArrayConstructable
{
    public function __construct(
        private string $title,
        private string $subtitle,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function getHeader(): string
    {
        $data = [];

        if ('' !== $this->title) {
            $data[] = $this->title;
        }

        if ('' !== $this->subtitle) {
            $data[] = $this->subtitle;
        }

        return Transform::toPlainText(implode(' - ', $data));
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
        ];
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            Transform::toString($data['title'] ??= ''),
            Transform::toString($data['subtitle'] ??= ''),
        );
    }
}
