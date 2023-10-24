<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\Composer\Fixer;

use PHPUnit\Framework\TestCase;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\Composer\Fixer\FeatureDevMasterPackageFixerStep;

class FeatureDevMasterPackageFixerStepTest extends TestCase
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE = 'spryker-feature/spryker-core 202204.0 requires spryker/glue-http ^0.2.0 -> found spryker/glue-http[0.2.0] but it conflicts with your root composer.json require (0.3.0)';

    /**
     * @return void
     */
    public function testIsApplicableWhenIsFeatureToDevMasterDisabled(): void
    {
        // Arrange
        $fixer = new FeatureDevMasterPackageFixerStep(
            $this->createMock(PackageManagerAdapterInterface::class),
        );
        $stepsResponseDto = new StepsResponseDto(false, static::ERROR_MESSAGE);

        // Act
        $result = $fixer->isApplicable($stepsResponseDto);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function testIsApplicable(): void
    {
        // Arrange
        $fixer = new FeatureDevMasterPackageFixerStep(
            $this->createMock(PackageManagerAdapterInterface::class),
            true,
        );
        $stepsResponseDto = new StepsResponseDto(false, static::ERROR_MESSAGE);

        // Act
        $result = $fixer->isApplicable($stepsResponseDto);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testRunSkip(): void
    {
        // Arrange
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->expects($this->never())
            ->method('require');

        $fixer = new FeatureDevMasterPackageFixerStep($packageManagerAdapter);
        $stepsResponseDtoInput = new StepsResponseDto(false, 'n/a');

        // Act
        $stepsResponseDto = $fixer->run($stepsResponseDtoInput);

        // Assert
        $this->assertEquals(
            $stepsResponseDtoInput,
            $stepsResponseDto,
        );
    }

    /**
     * @return void
     */
    public function testIsNotApplicable(): void
    {
        // Arrange
        $fixer = new FeatureDevMasterPackageFixerStep(
            $this->createMock(PackageManagerAdapterInterface::class),
        );
        $stepsResponseDto = new StepsResponseDto(false, 'spryker/spryker-core');

        // Act
        $result = $fixer->isApplicable($stepsResponseDto);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function testRunFix(): void
    {
        // Arrange
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->expects($this->once())
            ->method('require')
            ->willReturn(new PackageManagerResponseDto(true));

        $fixer = new FeatureDevMasterPackageFixerStep($packageManagerAdapter);
        $stepsResponseDto = new StepsResponseDto(false, static::ERROR_MESSAGE);

        // Act
        $stepsResponseDto = $fixer->run($stepsResponseDto);

        // Assert
        $this->assertTrue($stepsResponseDto->isSuccessful());
        $this->assertSame(
            sprintf(
                'Versions were changed to %s for %s feature package(s)',
                FeatureDevMasterPackageFixerStep::ALIAS_DEV_MASTER,
                1,
            ),
            $stepsResponseDto->getOutputMessage(),
        );
    }

    /**
     * @return void
     */
    public function testRunWithFailed(): void
    {
        // Arrange
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->expects($this->once())
            ->method('require')
            ->willReturn(new PackageManagerResponseDto(false, 'error-output'));

        $fixer = new FeatureDevMasterPackageFixerStep($packageManagerAdapter);
        $stepsResponseDto = new StepsResponseDto(false, static::ERROR_MESSAGE);

        // Act
        $stepsResponseDto = $fixer->run($stepsResponseDto);

        // Assert
        $this->assertFalse($stepsResponseDto->isSuccessful());
        $this->assertStringContainsString(
            'error-output',
            (string)$stepsResponseDto->getOutputMessage(),
        );
    }

    /**
     * @return void
     */
    public function testRunCanNotUpdateFeatureDevMaster(): void
    {
        // Arrange
        $responseDto = new PackageManagerResponseDto(false);
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->expects($this->once())
            ->method('require')
            ->willReturn(new PackageManagerResponseDto(true));

        $fixer = new FeatureDevMasterPackageFixerStep($packageManagerAdapter);
        $stepsResponseDto = new StepsResponseDto(false, static::ERROR_MESSAGE);

        // Act
        $stepsResponse = $fixer->run($stepsResponseDto);

        // Assert
        $this->assertSame($stepsResponseDto, $stepsResponse);
    }
}
