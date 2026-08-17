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
use Upgrade\Application\Strategy\Composer\Fixer\FeatureDevMasterPackageUpgradeFixer;

class FeatureDevMasterPackageUpgradeFixerTest extends TestCase
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE = 'spryker-feature/spryker-core 202204.0 requires spryker/glue-http ^0.2.0 -> found spryker/glue-http[0.2.0] but it conflicts with your root composer.json require (0.3.0)';

    public function testIsReRunStep(): void
    {
        // Arrange
        $fixer = new FeatureDevMasterPackageUpgradeFixer(
            $this->createMock(PackageManagerAdapterInterface::class),
        );

        // Act
        $result = $fixer->isReRunStep();

        // Assert
        $this->assertTrue($result);
    }

    public function testIsApplicableWhenIsFeatureToDevMasterDisabled(): void
    {
        // Arrange
        $fixer = new FeatureDevMasterPackageUpgradeFixer(
            $this->createMock(PackageManagerAdapterInterface::class),
        );
        $packageManagerResponseDto = new PackageManagerResponseDto(false, static::ERROR_MESSAGE);

        // Act
        $result = $fixer->isApplicable($this->createMock(ReleaseGroupDto::class), $packageManagerResponseDto);

        // Assert
        $this->assertFalse($result);
    }

    public function testIsApplicable(): void
    {
        // Arrange
        $fixer = new FeatureDevMasterPackageUpgradeFixer(
            $this->createMock(PackageManagerAdapterInterface::class),
            true,
        );
        $packageManagerResponseDto = new PackageManagerResponseDto(false, static::ERROR_MESSAGE);

        // Act
        $result = $fixer->isApplicable($this->createMock(ReleaseGroupDto::class), $packageManagerResponseDto);

        // Assert
        $this->assertTrue($result);
    }

    public function testRunSkip(): void
    {
        // Arrange
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->expects($this->never())
            ->method('require');

        $fixer = new FeatureDevMasterPackageUpgradeFixer($packageManagerAdapter);
        $packageManagerResponseDtoInput = new PackageManagerResponseDto(false, 'n/a');

        // Act
        $packageManagerResponseDto = $fixer->run($this->createMock(ReleaseGroupDto::class), $packageManagerResponseDtoInput);

        // Assert
        $this->assertEquals(
            null,
            $packageManagerResponseDto,
        );
    }

    public function testIsNotApplicable(): void
    {
        // Arrange
        $fixer = new FeatureDevMasterPackageUpgradeFixer(
            $this->createMock(PackageManagerAdapterInterface::class),
        );
        $packageManagerResponseDto = new PackageManagerResponseDto(false, 'spryker/spryker-core');

        // Act
        $result = $fixer->isApplicable($this->createMock(ReleaseGroupDto::class), $packageManagerResponseDto);

        // Assert
        $this->assertFalse($result);
    }

    public function testRunFix(): void
    {
        // Arrange
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->expects($this->once())
            ->method('require')
            ->willReturn(new PackageManagerResponseDto(true));

        $fixer = new FeatureDevMasterPackageUpgradeFixer($packageManagerAdapter);
        $packageManagerResponseDto = new PackageManagerResponseDto(false, static::ERROR_MESSAGE);

        // Act
        $packageManagerResponseDto = $fixer->run($this->createMock(ReleaseGroupDto::class), $packageManagerResponseDto);

        // Assert
        $this->assertNotNull($packageManagerResponseDto);
        $this->assertTrue($packageManagerResponseDto->isSuccessful());
    }

    public function testRunWithFailed(): void
    {
        // Arrange
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->expects($this->once())
            ->method('require')
            ->willReturn(new PackageManagerResponseDto(false, 'error-output'));

        $fixer = new FeatureDevMasterPackageUpgradeFixer($packageManagerAdapter);
        $packageManagerResponseDto = new PackageManagerResponseDto(false, static::ERROR_MESSAGE);

        // Act
        $packageManagerResponseDto = $fixer->run($this->createMock(ReleaseGroupDto::class), $packageManagerResponseDto);

        // Assert
        $this->assertNotNull($packageManagerResponseDto);
        $this->assertFalse($packageManagerResponseDto->isSuccessful());
        $this->assertStringContainsString(
            'error-output',
            (string)$packageManagerResponseDto->getOutputMessage(),
        );
    }

    public function testRunCanNotUpdateFeatureDevMaster(): void
    {
        // Arrange
        $responseDto = new PackageManagerResponseDto(true);
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->expects($this->once())
            ->method('require')
            ->willReturn($responseDto);

        $fixer = new FeatureDevMasterPackageUpgradeFixer($packageManagerAdapter);
        $packageManagerResponseDto = new PackageManagerResponseDto(false, static::ERROR_MESSAGE);

        // Act
        $packageManagerResponse = $fixer->run($this->createMock(ReleaseGroupDto::class), $packageManagerResponseDto);

        // Assert
        $this->assertEquals($responseDto, $packageManagerResponse);
    }

    public function testRunFixWithNoPackage(): void
    {
        // Arrange
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $fixer = new FeatureDevMasterPackageUpgradeFixer($packageManagerAdapter);
        $packageManagerResponseDto = new PackageManagerResponseDto(false, 'test/package');

        // Act
        $packageManagerResponseDto = $fixer->run($this->createMock(ReleaseGroupDto::class), $packageManagerResponseDto);

        // Assert
        $this->assertNull($packageManagerResponseDto);
    }
}
