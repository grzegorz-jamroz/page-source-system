<?php

declare(strict_types=1);

namespace PageSourceSystem\Utility;

class Asset
{
    public function __construct(
        private string $entryDirectory,
        private string $entryName,
        private string $extension,
        private string $entrypointName = 'public'
    ) {
        $this->entrypointName = sprintf('%s/%s', $entryDirectory, $this->entrypointName);
    }

    public function getSrc(): string
    {
        $path = sprintf('%s/%s*.%s', $this->entrypointName, $this->entryName, $this->extension);
        $pathames = glob($path);

        if (false === $pathames) {
            throw new \Exception(sprintf('Unable to find path %s', $path));
        }

        $path = $pathames[0] ?? sprintf('%s/%s.%s', $this->entrypointName, $this->entryName, $this->extension);

        return str_replace($this->entrypointName, '', $path);
    }
}
