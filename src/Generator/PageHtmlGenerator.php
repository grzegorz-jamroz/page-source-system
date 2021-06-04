<?php

declare(strict_types=1);

namespace PageSourceSystem\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use HtmlCreator\ContentBuilder;
use HtmlCreator\ElementInterface;
use HtmlCreator\Helmet;
use HtmlCreator\PageBuilder;
use HtmlCreator\PageFactory;
use PageSourceSystem\Domain\Header;
use PageSourceSystem\Domain\Page;
use PageSourceSystem\Repository\ComponentRepository;
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
     * @var array<string, mixed>
     */
    private array $footer;

    public function __construct(
        private Page $page,
        private Asset $jsAsset,
        private Asset $cssAsset,
        private ComponentRepository $componentRepository,
        private string $appDataDir,
        private string $appRenderDir,
    ) {
        $this->setSeo();
        $this->setElements();
    }

    public function generate(): void
    {
        $this->storeHtml($this->getHtml());
    }

    private function getHtml(): string
    {
        return (new PageFactory($this->getPageBuilder()))->getHtml();
    }

    private function getPageBuilder(): PageBuilder
    {
        return new PageBuilder(
            $this->page->getLanguage(),
            $this->jsAsset->getSrc(),
            $this->cssAsset->getSrc(),
            Helmet::createFromArray($this->seo),
            $this->getContentBuilder(),
        );
    }

    private function storeHtml(string $html): void
    {
        (new PageHtmlStorage(
            $this->appRenderDir,
            $this->page->getLanguage(),
            $this->page->getUuid()
        ))->overwrite($html);
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
        $this->seo = $this->componentRepository->getComponentData(
            $this->page->getSeoUuid()
        );
    }

    private function setElements(): void
    {
        $mainComponents = new ArrayCollection();
        $pageComponents = $this->page->getComponents();

        foreach ($pageComponents as $uuid) {
            $component = $this->componentRepository->getComponent($uuid);
            /** @var ElementInterface $htmlClass */
            $htmlClass = $component->getHtmlClass();
            $role = $htmlClass::getHtmlRole();
            $componentData = $component->jsonSerialize();

            if ($this->isHeader($role)) {
                $header = Transform::toArray($componentData['header'] ??= []);
                $this->header = (Header::createFromArray($header))->getHeader();

                continue;
            }

            if ($this->isNavbar($role)) {
                $this->navbar = $componentData;

                continue;
            }

            if ($this->isFooter($role)) {
                $this->footer = $componentData;

                continue;
            }

            $mainComponents->set($uuid, $componentData);
        }
    }

    private function isHeader(string $role): bool
    {
        return 'header' === $role && !isset($this->header);
    }

    private function isNavbar(string $role): bool
    {
        return 'navbar' === $role && !isset($this->navbar);
    }

    private function isFooter(string $role): bool
    {
        return 'footer' === $role && !isset($this->footer);
    }
}
