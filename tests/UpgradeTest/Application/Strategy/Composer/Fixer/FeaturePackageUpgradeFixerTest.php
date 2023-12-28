<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\Composer\Fixer;

use PHPUnit\Framework\TestCase;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Strategy\Composer\Fixer\FeaturePackageUpgradeFixer;

class FeaturePackageUpgradeFixerTest extends TestCase
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE = 'spryker-feature/spryker-core 202204.0 requires spryker/glue-http ^0.2.0 -> found spryker/glue-http[0.2.0] but it conflicts with your root composer.json require (0.3.0)';

    /**
     * @return void
     */
    public function testIsReRunStep(): void
    {
        // Arrange
        $fixer = new FeaturePackageUpgradeFixer(
            $this->createMock(PackageManagerAdapterInterface::class),
        );

        // Act
        $result = $fixer->isReRunStep();

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testIsApplicableWhenReleaseGroupIntegratorEnabled(): void
    {
        // Arrange
        $fixer = new FeaturePackageUpgradeFixer(
            $this->createMock(PackageManagerAdapterInterface::class),
            true,
        );
        $packageManagerResponseDto = new PackageManagerResponseDto(false, static::ERROR_MESSAGE);

        // Act
        $result = $fixer->isApplicable($this->createMock(ReleaseGroupDto::class), $packageManagerResponseDto);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function testIsApplicable(): void
    {
        // Arrange
        $fixer = new FeaturePackageUpgradeFixer(
            $this->createMock(PackageManagerAdapterInterface::class),
        );
        $packageManagerResponseDto = new PackageManagerResponseDto(false, static::ERROR_MESSAGE);

        // Act
        $result = $fixer->isApplicable($this->createMock(ReleaseGroupDto::class), $packageManagerResponseDto);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testIsNotApplicable(): void
    {
        // Arrange
        $fixer = new FeaturePackageUpgradeFixer(
            $this->createMock(PackageManagerAdapterInterface::class),
        );
        $packageManagerResponseDto = new PackageManagerResponseDto(false, 'spryker-feature1/spryker-core');

        // Act
        $result = $fixer->isApplicable($this->createMock(ReleaseGroupDto::class), $packageManagerResponseDto);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function testRunFixWithNoPackage(): void
    {
        // Arrange
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $fixer = new FeaturePackageUpgradeFixer($packageManagerAdapter);
        $packageManagerResponseDto = new PackageManagerResponseDto(false, 'test/package');

        // Act
        $packageManagerResponseDto = $fixer->run($this->createMock(ReleaseGroupDto::class), $packageManagerResponseDto);

        // Assert
        $this->assertNull($packageManagerResponseDto);
    }

    /**
     * @return void
     */
    public function testRunFix(): void
    {
        // Arrange
        $responseDto = new PackageManagerResponseDto(true);
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->expects($this->once())
            ->method('remove')
            ->willReturn($responseDto);
        $packageManagerAdapter->expects($this->once())
            ->method('require')
            ->willReturn($responseDto);
        $packageManagerAdapter->expects($this->once())
            ->method('getComposerLockFile')
            ->willReturn(['packages' => [['name' => 'name']]]);

        $fixer = new FeaturePackageUpgradeFixer($packageManagerAdapter);
        $packageManagerResponseDto = new PackageManagerResponseDto(false, static::ERROR_MESSAGE);

        // Act
        $packageManagerResponseDto = $fixer->run($this->createMock(ReleaseGroupDto::class), $packageManagerResponseDto);

        // Assert
        $this->assertNotNull($packageManagerResponseDto);
        $this->assertTrue($packageManagerResponseDto->isSuccessful());
    }

    /**
     * @return void
     */
    public function testRunCanNotRequirePackages(): void
    {
        // Arrange
        $packageManagerResponseDto = new PackageManagerResponseDto(false, static::ERROR_MESSAGE);
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->expects($this->once())
            ->method('require')
            ->willReturn($packageManagerResponseDto);

        $fixer = new FeaturePackageUpgradeFixer($packageManagerAdapter);

        // Act
        $packageManagerResponse = $fixer->run($this->createMock(ReleaseGroupDto::class), $packageManagerResponseDto);

        // Assert
        $this->assertSame($packageManagerResponseDto, $packageManagerResponse);
    }

    /**
     * @return void
     */
    public function testRunCanNotRemoveFeature(): void
    {
        // Arrange
        $responseDto = new PackageManagerResponseDto(false, static::ERROR_MESSAGE);
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->expects($this->once())
            ->method('remove')
            ->willReturn($responseDto);
        $packageManagerAdapter->expects($this->once())
            ->method('require')
            ->willReturn(new PackageManagerResponseDto(true));

        $fixer = new FeaturePackageUpgradeFixer($packageManagerAdapter);
        $packageManagerResponseDto = new PackageManagerResponseDto(false, static::ERROR_MESSAGE);

        // Act
        $packageManagerResponse = $fixer->run($this->createMock(ReleaseGroupDto::class), $packageManagerResponseDto);

        // Assert
        $this->assertEquals($responseDto, $packageManagerResponse);
    }
}
