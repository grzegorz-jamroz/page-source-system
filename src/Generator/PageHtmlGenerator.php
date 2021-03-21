<?php

declare(strict_types=1);

namespace PageSourceSystem\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use HtmlCreator\ContentBuilder;
use HtmlCreator\Helmet;
use HtmlCreator\PageBuilder;
use HtmlCreator\PageFactory;
use PageSourceSystem\Domain\ComponentInfo;
use PageSourceSystem\Domain\Header;
use PageSourceSystem\Domain\Page;
use PageSourceSystem\Storage\ComponentInfoStorage;
use PageSourceSystem\Storage\ComponentStorage;
use PageSourceSystem\Storage\PageHtmlStorage;
use PageSourceSystem\Utility\Asset;
use PlainDataTransformer\Transform;

class PageHtmlGenerator implements GeneratorInterface
{
    private string $header;

    /**
     * @var array<string, mixed>
     */
    private array $seo;

    /**
     * @var array<string, mixed>
     */
    private array $navbar;

    /**
     * @var array<int, array>
     */
    private array $mainComponents;

    /**
     * @var array<int, array>
     */
    private array $footer;

    public function __construct(
        private Page $page,
        private ComponentInfoStorage $componentInfoStorage,
        private Asset $jsAsset,
        private Asset $cssAsset,
        private string $appDataDir,
        private string $appRenderDir,
    ) {
        $this->setSeo();
        $this->setElements();
    }

    public function generate(): void
    {
        $language = $this->page->getLanguage();
        $pageBuilder = new PageBuilder(
            $language,
            $this->jsAsset->getSrc(),
            $this->cssAsset->getSrc(),
            Helmet::createFromArray($this->seo),
            $this->getContentBuilder(),
        );
        $html = (new PageFactory($pageBuilder))->getHtml();
        (new PageHtmlStorage(
            $this->appRenderDir,
            $language,
            $this->page->getUuid()
        ))->overwrite($html);
    }

    private function setElements(): void
    {
        $mainComponents = new ArrayCollection();
        $pageComponents = $this->page->getComponents();
        $language = $this->page->getLanguage();

        foreach ($pageComponents as $uuid) {
            $component = $this->getComponentData($language, $uuid);
            $componentInfo = $this->componentInfoStorage->getComponentInfo($component);
            $component['className'] = $componentInfo->getHtmlClass();

            if ($this->isHeaderComponent($componentInfo) && !isset($this->header)) {
                $header = Transform::toArray($component['header'] ??= []);
                $this->header = (Header::createFromArray($header))->getHeader();
            }

            if ($this->isNavbarComponent($componentInfo) && !isset($this->navbar)) {
                $this->navbar = $component;
            }

            if ($this->isFooterComponent($componentInfo) && !isset($this->footer)) {
                $this->footer = $component;
            }

            if ($this->isMainComponent($componentInfo)) {
                $component['htmlClass'] = $componentInfo->getHtmlClass();
                $mainComponents->set($uuid, $component);
            }
        }

        $this->mainComponents = $mainComponents->toArray();
    }

    private function isHeaderComponent(ComponentInfo $componentInfo): bool
    {
        return 'header' === $componentInfo->getType();
    }

    private function isNavbarComponent(ComponentInfo $componentInfo): bool
    {
        return 'navbar' === $componentInfo->getType();
    }

    private function isMainComponent(ComponentInfo $componentInfo): bool
    {
        if ('' === $componentInfo->getHtmlClass()) {
            return false;
        }

        $mainTypes = ['header', 'section', 'article', 'aside', 'footer'];

        return in_array($componentInfo->getType(), $mainTypes);
    }

    private function isFooterComponent(ComponentInfo $componentInfo): bool
    {
        return 'footer' === $componentInfo->getType();
    }

    private function getContentBuilder(): ContentBuilder
    {
        return ContentBuilder::createFromArray([
            'header' => $this->header ??= '',
            'navbar' => $this->navbar ??= [],
            'main' => [
                'items' => $this->mainComponents ??= [],
            ],
            'footer' => $this->footer ??= [],
        ]);
    }

    private function setSeo(): void
    {
        $uuid = $this->page->getSeoUuid();
        $language = $this->page->getLanguage();
        $this->seo = $this->getComponentData(
            $language,
            $uuid
        );
    }

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
}
