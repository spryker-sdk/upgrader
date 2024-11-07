<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\ReleaseApp\Processor;

use Psr\Log\LoggerInterface;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcher;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupUpgrader;
use Upgrade\Application\Strategy\UpgradeFixerInterface;

class ReleaseGroupUpgraderTest extends KernelTestCase
{
    /**
     * @return void
     */
    public function testRequireWithAlternativePackages(): void
    {
        // Arrange
        $moduleCollection = new ModuleDtoCollection([new ModuleDto('spryker/cart', '200402.0')]);
        $featureCollection = new ModuleDtoCollection([new ModuleDto('spryker-feature/cart', '200402.0')]);
        $moduleWithFeatureCollection = clone $moduleCollection;
        foreach ($featureCollection->toArray() as $feature) {
            $moduleWithFeatureCollection->add($feature);
        }

        $moduleFetcherMock = $this->createMock(ModuleFetcher::class);
        $invocations = [];
        $moduleFetcherMock->expects($this->exactly(2))
            ->method('require')
            ->willReturnCallback(function ($modules) use (&$invocations) {
                $invocations[] = $modules;

                return new PackageManagerResponseDto(false);
            });
        $loggerMock = $this->createMock(LoggerInterface::class);
        $releaseGroupUpgrader = new ReleaseGroupUpgrader($moduleFetcherMock, $loggerMock, []);
        $releaseGroupMock = $this->createMock(ReleaseGroupDto::class);
        $releaseGroupMock->method('getModuleCollection')->willReturn($moduleCollection);
        $releaseGroupMock->method('getFeaturePackages')->willReturn($featureCollection);
        // Act
        $packageManagerResponseDto = $releaseGroupUpgrader->upgrade($releaseGroupMock);

        // Assert
        $this->assertNotNull($releaseGroupUpgrader);
        $this->assertFalse($packageManagerResponseDto->isSuccessful());
        $this->assertEquals([$moduleWithFeatureCollection, $moduleCollection], $invocations);
    }

    /**
     * @return void
     */
    public function testRequireWithoutRunFixer(): void
    {
        // Arrange
        $moduleFetcherMock = $this->createMock(ModuleFetcher::class);
        $moduleFetcherMock->method('require')->willReturn(new PackageManagerResponseDto(true));
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->never())->method('info');
        $releaseGroupUpgrader = new ReleaseGroupUpgrader($moduleFetcherMock, $loggerMock, []);

        // Act
        $packageManagerResponseDto = $releaseGroupUpgrader->upgrade($this->createMock(ReleaseGroupDto::class));

        // Assert
        $this->assertNotNull($releaseGroupUpgrader);
        $this->assertTrue($packageManagerResponseDto->isSuccessful());
    }

    /**
     * @return void
     */
    public function testRequireWithRunFixer(): void
    {
        // Arrange
        $moduleDtoCollection = new ModuleDtoCollection([
            new ModuleDto('spryker/cart', '2.1.9', 'minor'),
            new ModuleDto('spryker-shup/cart-page', '1.1.9', 'minor'),
            new ModuleDto('spryker/merchant', '3.2.1', 'minor'),
        ]);
        $releaseGroupDtoMock = $this->createMock(ReleaseGroupDto::class);
        $releaseGroupDtoMock->method('getModuleCollection')->willReturn($moduleDtoCollection);
        $moduleFetcherMock = $this->createMock(ModuleFetcher::class);
        $moduleFetcherMock->method('require')->willReturn(
            new PackageManagerResponseDto(false),
            new PackageManagerResponseDto(true),
        )->with($moduleDtoCollection);

        $logMessages = [];
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->exactly(4))
            ->method('info')
            ->willReturnCallback(function (...$args) use (&$logMessages) {
                $logMessages[] = $args;

                return $logMessages;
            });

        $fixer1 = $this->getMockBuilder(UpgradeFixerInterface::class);
        $fixer1->setMockClassName('FixerOne');
        $fixer1 = $fixer1->getMock();
        $fixer1->expects($this->once())->method('isApplicable')->willReturn(false);
        $fixer1->expects($this->never())->method('run');
        $fixer2 = $this->getMockBuilder(UpgradeFixerInterface::class);
        $fixer2->setMockClassName('FixerTwo');
        $fixer2 = $fixer2->getMock();
        $fixer2->expects($this->once())->method('isApplicable')->willReturn(true);
        $fixer2->expects($this->once())->method('run')->willReturn(new PackageManagerResponseDto(true));
        $fixer2->expects($this->once())->method('isReRunStep')->willReturn(true);
        $releaseGroupUpgrader = new ReleaseGroupUpgrader(
            $moduleFetcherMock,
            $loggerMock,
            [
                $fixer1,
                $fixer2,
            ],
        );

        // Act
        $packageManagerResponseDto = $releaseGroupUpgrader->upgrade($releaseGroupDtoMock);

        // Assert
        $this->assertNotNull($releaseGroupUpgrader);
        $this->assertTrue($packageManagerResponseDto->isSuccessful());

        $expectedLogMessages = [
            ['Release Group `0` is failed. Trying to fix it', [null]],
            ['Fixer `FixerOne` is not applicable'],
            ['`FixerTwo` fixer is applying'],
            ['Run release group upgrade after fixer.'],
        ];

        $this->assertCount(4, $logMessages);
        foreach ($expectedLogMessages as $index => $expected) {
            $this->assertSame($expected[0], $logMessages[$index][0]);
            if (isset($expected[1])) {
                $this->assertSame($expected[1], $logMessages[$index][1] ?? null);
            }
        }
    }

    /**
     * @return void
     */
    public function testRequireWithRunFixerThatFailed(): void
    {
        // Arrange
        $moduleDtoCollection = new ModuleDtoCollection([
            new ModuleDto('spryker/cart', '2.1.9', 'minor'),
            new ModuleDto('spryker-shup/cart-page', '1.1.9', 'minor'),
            new ModuleDto('spryker/merchant', '3.2.1', 'minor'),
        ]);
        $releaseGroupDtoMock = $this->createMock(ReleaseGroupDto::class);
        $releaseGroupDtoMock->method('getModuleCollection')->willReturn($moduleDtoCollection);
        $moduleFetcherMock = $this->createMock(ModuleFetcher::class);
        $moduleFetcherMock->method('require')->willReturn(
            new PackageManagerResponseDto(false),
            new PackageManagerResponseDto(true),
        )->with($moduleDtoCollection);

        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->method('warning')->with('Fixer `Fixer2` is failed', [null]);

        $infoMessages = [];
        $loggerMock->expects($this->exactly(3))->method('info')
            ->willReturnCallback(function ($message, $context = []) use (&$infoMessages) {
                $infoMessages[] = [$message, $context];

                return $infoMessages;
            });

        $fixer1 = $this->getMockBuilder(UpgradeFixerInterface::class)
            ->setMockClassName('Fixer1')
            ->getMock();
        $fixer1->expects($this->once())->method('isApplicable')->willReturn(false);
        $fixer1->expects($this->never())->method('run');
        $fixer2 = $this->getMockBuilder(UpgradeFixerInterface::class)
            ->setMockClassName('Fixer2')
            ->getMock();
        $fixer2->expects($this->once())->method('isApplicable')->willReturn(true);
        $fixer2->expects($this->once())->method('run')->willReturn(new PackageManagerResponseDto(false));
        $releaseGroupUpgrader = new ReleaseGroupUpgrader(
            $moduleFetcherMock,
            $loggerMock,
            [
                $fixer1,
                $fixer2,
            ],
        );

        // Act
        $packageManagerResponseDto = $releaseGroupUpgrader->upgrade($releaseGroupDtoMock);

        // Assert
        $this->assertNotNull($releaseGroupUpgrader);
        $this->assertFalse($packageManagerResponseDto->isSuccessful());
        $this->assertSame('Release Group `0` is failed. Trying to fix it', $infoMessages[0][0]);
        $this->assertMatchesRegularExpression('/Fixer `.*` is not applicable/', $infoMessages[1][0]);
        $this->assertMatchesRegularExpression('/`.*` fixer is applying/', $infoMessages[2][0]);
    }
}
