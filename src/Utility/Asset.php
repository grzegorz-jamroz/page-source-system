<?php

declare(strict_types=1);

namespace PageSourceSystem\Utility;

use PageSourceSystem\Exception\AssetNotExists;

class Asset
{
    private string $directory;
    private string $path;

    public function __construct(
        string $projectDirectory,
        private string $entryName,
        private string $extension,
        private string $entryPath = '',
        string $entrypointName = 'public'
    ) {
        $this->setDirectory($projectDirectory, $entrypointName);
        $this->entryName = trim($this->entryName, '/');
        $this->extension = trim($this->extension, '/');
        $this->entryPath = trim($this->entryPath, '/');
        $this->path = sprintf('%s/%s*.%s', $this->directory, $this->entryName, $this->extension);
    }

    public function getSrc(): string
    {
        $pathames = glob($this->path);

        if ($pathames === false || $pathames === []) {
            throw new AssetNotExists($this->getSrcPath($this->path));
        }

        return $this->getSrcPath($pathames[0]);
    }

    private function setDirectory(
        string $projectDirectory,
        string $entrypointName,
    ): void {
        $entrypointName = trim($entrypointName, '/');
        $directory = sprintf('%s/%s/%s', $projectDirectory, $entrypointName, $this->entryPath);
        $directory = preg_replace('~/{2,}~', '/', $directory) ?? $directory;
        $this->directory = rtrim($directory, '/');
    }

    private function getSrcPath(string $path): string
    {
        return sprintf('/%s%s', $this->entryPath, str_replace($this->directory, '', $path));
    }
}
