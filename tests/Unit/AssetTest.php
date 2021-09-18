<?php

declare(strict_types=1);

namespace Tests\Unit;

use PageSourceSystem\Exception\AssetNotExists;
use PageSourceSystem\Utility\Asset;
use PHPStan\Testing\TestCase;

class AssetTest extends TestCase
{
    public function testShouldReturnAssetSourceWhenPhpFileGiven(): void
    {
        // Given
        $assetUnderTest = new Asset(
            dirname(__DIR__, 2),
            'AssetTest',
            'php',
            'Unit',
            'tests'
        );
    
        // When & Then
        $this->assertEquals(
            '/Unit/AssetTest.php',
            $assetUnderTest->getSrc()
        );
    }

    public function testShouldReturnAssetSourceWhenPhpFileGivenWithRedundantSlashes(): void
    {
        // Given
        $assetUnderTest = new Asset(
            dirname(__DIR__, 2),
            '///AssetTest/',
            '//php/',
            '///Unit//',
            '///tests/'
        );

        // When & Then
        $this->assertEquals(
            '/Unit/AssetTest.php',
            $assetUnderTest->getSrc()
        );
    }

    public function testShouldThrowAssetNotExistsWhenGivenCssFileNotExists(): void
    {
        // Expect
        $this->expectException(AssetNotExists::class);
        $this->expectExceptionMessage('Unable to find Asset under path "/build/app*.css"');

        // Given
        $assetUnderTest = new Asset(
            dirname(__DIR__, 2),
            'app',
            'css',
            'build'
        );

        // When
        $assetUnderTest->getSrc();
    }
}
