<?php
declare(strict_types=1);

namespace PageSourceSystem\Utility;

use Ifrost\PageSourceComponents\AbstractComponent;
use PageSourceSystem\Repository\ComponentRepository;
use PageSourceSystem\Utility\Mapper\ComponentMapper;

class PageSeoDataTransformer
{
    public function __construct(private ComponentRepository $componentRepository)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function getCombinedWithPrimarySeo(array $seoData): array
    {
        $language = (string) $seoData['language'] ?? '';
        $primarySeoData = $this->componentRepository->getPrimarySeoData($language);
        $primarySeoData = $this->filterSeoData($primarySeoData);
        $seoData = array_merge($seoData, $primarySeoData);

        return ComponentMapper::getWithFieldsAllowedForRender($seoData);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function filterSeoData(array $data): array
    {
        return array_filter(
            $data,
            fn($value, $key) => !in_array($key, AbstractComponent::NOT_EDITABLE_FIELDS),
            ARRAY_FILTER_USE_BOTH
        );
    }
}
