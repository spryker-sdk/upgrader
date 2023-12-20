<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\Composer\Fixer;

use PHPUnit\Framework\TestCase;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Strategy\Composer\Fixer\BackportUpgradeFixer;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Domain\Entity\Package;

class BackportUpgradeFixerTest extends TestCase
{
    /**
     * @return void
     */
    public function testIsApplicable(): void
    {
        // Arrange
        $fixer = new BackportUpgradeFixer(
            $this->createMock(PackageManagerAdapterInterface::class),
        );
        $packageManagerResponseDto = new PackageManagerResponseDto(false, 'test');
        $releaseGroupDtoMock = $this->createMock(ReleaseGroupDto::class);
        $releaseGroupDtoMock->method('getBackportModuleCollection')->willReturn(new ModuleDtoCollection(['test']));

        // Act
        $result = $fixer->isApplicable($releaseGroupDtoMock, $packageManagerResponseDto);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testIsNotApplicable(): void
    {
        // Arrange
        $packageManagerAdapterMock = $this->createMock(PackageManagerAdapterInterface::class);
        $fixer = new BackportUpgradeFixer($packageManagerAdapterMock);
        $packageManagerResponseDto = new PackageManagerResponseDto(false, 'test');

        // Act
        $result = $fixer->isApplicable($this->createMock(ReleaseGroupDto::class), $packageManagerResponseDto);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function testRunFix(): void
    {
        // Arrange
        $packageManagerAdapterMock = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapterMock->method('require')->with(new PackageCollection([
            new Package('spryker/cart', '2.1.9'),
            new Package('spryker-shup/cart-page', '0.1.9'),
            new Package('spryker/merchant', '0.2.1'),
        ]))->willReturn(new PackageManagerResponseDto(true));
        $responseDto = new PackageManagerResponseDto(false);
        $releaseGroupDtoMock = $this->createMock(ReleaseGroupDto::class);
        $releaseGroupDtoMock->method('getModuleCollection')->willReturn(new ModuleDtoCollection([
            new ModuleDto('spryker/cart', '2.1.9', 'minor'),
            new ModuleDto('spryker-shup/cart-page', '1.1.9', 'minor'),
            new ModuleDto('spryker/merchant', '3.2.1', 'minor'),
        ]));
        $releaseGroupDtoMock->method('getBackportModuleCollection')->willReturn(new ModuleDtoCollection([
            new ModuleDto('spryker/cart-new', '2.1.9', 'minor'),
            new ModuleDto('spryker-shup/cart-page', '0.1.9', 'minor'),
            new ModuleDto('spryker/merchant', '0.2.1', 'minor'),
        ]));
        $fixer = new BackportUpgradeFixer($packageManagerAdapterMock);
        $packageManagerResponseDto = new PackageManagerResponseDto(false, 'test');

        // Act
        $packageManagerResponseDto = $fixer->run($releaseGroupDtoMock, $packageManagerResponseDto);

        // Assert
        $this->assertNotNull($packageManagerResponseDto);
        $this->assertTrue($packageManagerResponseDto->isSuccessful());
    }
}
