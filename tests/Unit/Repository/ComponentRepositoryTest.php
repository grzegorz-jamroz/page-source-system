<?php

declare(strict_types=1);

namespace Tests\Unit\Repository;

use Ifrost\PageSourceComponents\ComponentCollection;
use PageSourceSystem\Component\BaseSeo;
use PageSourceSystem\Exception\ComponentNotExists;
use PageSourceSystem\Repository\ComponentRepository;
use PageSourceSystem\Repository\SettingsRepository;
use PHPStan\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ComponentRepositoryTest extends TestCase
{
    private SettingsRepository|MockObject $settingsRepositoryMock;

    protected function setUp(): void
    {
        $this->settingsRepositoryMock = $this->createMock(SettingsRepository::class);
    }

    public function testShouldMakeSeoComponent()
    {
        // Given
        $repositoryUnderTest = new ComponentRepository(
            $this->settingsRepositoryMock,
            new ComponentCollection([
                'Seo' => BaseSeo::createFromArray([]),
            ])
        );

        // When & Then
        $this->assertInstanceOf(BaseSeo::class, $repositoryUnderTest->makeComponent('Seo'));
    }

    public function testShouldThrowComponentNotExistsWhenTryingToMakeSeoComponentAndItNotExistsInComponentCollection()
    {
        // Expect
        $this->expectException(ComponentNotExists::class);
        $this->expectExceptionMessage('Component with typename "Seo" not exists.');

        // Given
        $repositoryUnderTest = new ComponentRepository(
            $this->settingsRepositoryMock,
            new ComponentCollection()
        );

        // When
        $repositoryUnderTest->makeComponent('Seo');
    }
}
