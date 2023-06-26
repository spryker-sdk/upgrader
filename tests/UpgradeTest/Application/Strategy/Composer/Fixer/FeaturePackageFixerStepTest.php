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
use Upgrade\Application\Strategy\Composer\Fixer\FeaturePackageFixerStep;

class FeaturePackageFixerStepTest extends TestCase
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE = 'spryker-feature/spryker-core 202204.0 requires spryker/glue-http ^0.2.0 -> found spryker/glue-http[0.2.0] but it conflicts with your root composer.json require (0.3.0)';

    /**
     * @return void
     */
    public function testIsApplicableWhenReleaseGroupIntegratorEnabled(): void
    {
        // Arrange
        $fixer = new FeaturePackageFixerStep(
            $this->createMock(PackageManagerAdapterInterface::class),
            true,
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
        $fixer = new FeaturePackageFixerStep(
            $this->createMock(PackageManagerAdapterInterface::class),
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
    public function testIsNotApplicable(): void
    {
        // Arrange
        $fixer = new FeaturePackageFixerStep(
            $this->createMock(PackageManagerAdapterInterface::class),
        );
        $stepsResponseDto = new StepsResponseDto(false, 'spryker-feature1/spryker-core');

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

        $fixer = new FeaturePackageFixerStep($packageManagerAdapter);
        $stepsResponseDto = new StepsResponseDto(false, static::ERROR_MESSAGE);

        // Act
        $stepsResponseDto = $fixer->run($stepsResponseDto);

        // Assert
        $this->assertTrue($stepsResponseDto->isSuccessful());
        $this->assertSame('Splitted 1 feature packages', $stepsResponseDto->getOutputMessage());
    }

    /**
     * @return void
     */
    public function testRunCanNotRequirePackages(): void
    {
        // Arrange
        $responseDto = new PackageManagerResponseDto(false);
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->expects($this->once())
            ->method('require')
            ->willReturn($responseDto);

        $fixer = new FeaturePackageFixerStep($packageManagerAdapter);
        $stepsResponseDto = new StepsResponseDto(false, static::ERROR_MESSAGE);

        // Act
        $stepsResponse = $fixer->run($stepsResponseDto);

        // Assert
        $this->assertSame($stepsResponseDto, $stepsResponse);
    }

    /**
     * @return void
     */
    public function testRunCanNotRemoveFeature(): void
    {
        // Arrange
        $responseDto = new PackageManagerResponseDto(false);
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->expects($this->once())
            ->method('remove')
            ->willReturn($responseDto);
        $packageManagerAdapter->expects($this->once())
            ->method('require')
            ->willReturn(new PackageManagerResponseDto(true));

        $fixer = new FeaturePackageFixerStep($packageManagerAdapter);
        $stepsResponseDto = new StepsResponseDto(false, static::ERROR_MESSAGE);

        // Act
        $stepsResponse = $fixer->run($stepsResponseDto);

        // Assert
        $this->assertSame($stepsResponseDto, $stepsResponse);
    }
}
