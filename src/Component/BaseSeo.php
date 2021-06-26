<?php

declare(strict_types=1);

namespace PageSourceSystem\Component;

use HtmlCreator\Helmet;
use Ifrost\PageSourceComponents\AbstractComponent;
use PlainDataTransformer\Transform;

class BaseSeo extends AbstractComponent
{
    const NOT_EDITABLE_FIELDS = [
        'uuid',
        '__typename',
        'language',
        'label',
    ];

    /**
     * @param array<int, array> $ogImages
     * @param array<int, array> $icons
     * @param array<int, array> $appleTouchIcons
     * @param array<int, array> $appleTouchPrecomposedIcons
     */
    public function __construct(
        private string $uuid,
        private string $language,
        private string $internalTitle,
        private string $title,
        private string $description,
        private string $url,
        private string $modifiedTime,
        private string $pageUrl,
        private string $themeColor,
        private string $image,
        private string $msapplicationTileImage,
        private string $sitemap,
        private string $manifest,
        private string $favicon,
        private array $ogImages,
        private array $icons,
        private array $appleTouchIcons,
        private array $appleTouchPrecomposedIcons,
    )
    {
        parent::__construct($uuid, $language, $internalTitle);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getTypename(): string
    {
        return 'Seo';
    }

    public static function getLabel(): string
    {
        return 'Seo';
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getInternalTitle(): string
    {
        return $this->internalTitle;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getModifiedTime(): string
    {
        return $this->modifiedTime;
    }

    public function getPageUrl(): string
    {
        return $this->pageUrl;
    }

    public function getThemeColor(): string
    {
        return $this->themeColor;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getMsapplicationTileImage(): string
    {
        return $this->msapplicationTileImage;
    }

    public function getSitemap(): string
    {
        return $this->sitemap;
    }

    public function getManifest(): string
    {
        return $this->manifest;
    }

    public function getFavicon(): string
    {
        return $this->favicon;
    }

    /**
     * @return array<int, array>
     */
    public function getOgImages(): array
    {
        return $this->ogImages;
    }

    /**
     * @return array<int, array>
     */
    public function getIcons(): array
    {
        return $this->icons;
    }

    /**
     * @return array<int, array>
     */
    public function getAppleTouchIcons(): array
    {
        return $this->appleTouchIcons;
    }

    /**
     * @return array<int, array>
     */
    public function getAppleTouchPrecomposedIcons(): array
    {
        return $this->appleTouchPrecomposedIcons;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'uuid' => $this->uuid,
            '__typename' => static::getTypename(),
            'language' => $this->language,
            'label' => static::getLabel(),
            'internalTitle' => $this->internalTitle,
            'relations' => static::getRelations(),
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'modifiedTime' => $this->modifiedTime,
            'pageUrl' => $this->pageUrl,
            'themeColor' => $this->themeColor,
            'image' => $this->image,
            'msapplicationTileImage' => $this->msapplicationTileImage,
            'sitemap' => $this->sitemap,
            'manifest' => $this->manifest,
            'favicon' => $this->favicon,
            'ogImages' => $this->ogImages,
            'icons' => $this->icons,
            'appleTouchIcons' => $this->appleTouchIcons,
            'appleTouchPrecomposedIcons' => $this->appleTouchPrecomposedIcons,
        ];
    }

    public function getHtmlClass(): string
    {
        return Helmet::class;
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            Transform::toString($data['uuid'] ??= ""),
            Transform::toString($data['language'] ??= ""),
            Transform::toString($data['internalTitle'] ??= ""),
            Transform::toString($data['title'] ??= ''),
            Transform::toString($data['description'] ??= ''),
            Transform::toString($data['url'] ??= ''),
            Transform::toString($data['modifiedTime'] ??= ''),
            Transform::toString($data['pageUrl'] ??= ''),
            Transform::toString($data['themeColor'] ??= '#ffffff'),
            Transform::toString($data['image'] ??= ''),
            Transform::toString($data['msapplicationTileImage'] ??= ''),
            Transform::toString($data['sitemap'] ??= ''),
            Transform::toString($data['manifest'] ??= ''),
            Transform::toString($data['favicon'] ??= ''),
            Transform::toArray($data['ogImages'] ??= []),
            Transform::toArray($data['icons'] ??= []),
            Transform::toArray($data['appleTouchIcons'] ??= []),
            Transform::toArray($data['appleTouchPrecomposedIcons'] ??= []),
        );
    }
}
