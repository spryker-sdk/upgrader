<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\Common\Step;

use PHPUnit\Framework\TestCase;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\Common\Step\PropelFixStep;

class PropelFixStepTest extends TestCase
{
    /**
     * @return void
     */
    public function testProcessShouldAddPropelPackageWhenItHasSpecificVersion(): void
    {
        // Arrange
        $packageManagerAdapterMock = $this->createPackageManagerAdapterMock(
            PropelFixStep::PACKAGE_NAME,
            PropelFixStep::LOCK_PACKAGE_VERSION,
            ['require' => []],
        );

        // Assert
        $packageManagerAdapterMock->expects($this->atLeastOnce())->method('require');

        // Arrange
        $propelFixStep = new PropelFixStep($packageManagerAdapterMock);

        // Act
        $propelFixStep->run(new StepsResponseDto());
    }

    /**
     * @return void
     */
    protected function testProcessShouldSkipAddingPropelPackageWhenReleaseGroupIntegratorEnabled(): void
    {
        // Arrange
        $packageManagerAdapterMock = $this->createPackageManagerAdapterMock(
            PropelFixStep::PACKAGE_NAME,
            PropelFixStep::LOCK_PACKAGE_VERSION,
            ['require' => [PropelFixStep::PACKAGE_NAME => '1.0.0']],
        );

        // Assert
        $packageManagerAdapterMock->expects($this->once())->method('getPackageVersion');
        $packageManagerAdapterMock->expects($this->never())->method('require');

        // Arrange
        $propelFixStep = new PropelFixStep($packageManagerAdapterMock, true);

        // Act
        $propelFixStep->run(new StepsResponseDto());
    }

    /**
     * @return void
     */
    protected function testProcessShouldSkipAddingPropelPackageWhenPackageInComposerRequire(): void
    {
        // Arrange
        $packageManagerAdapterMock = $this->createPackageManagerAdapterMock(
            PropelFixStep::PACKAGE_NAME,
            PropelFixStep::LOCK_PACKAGE_VERSION,
            ['require' => [PropelFixStep::PACKAGE_NAME => '1.0.0']],
        );

        // Assert
        $packageManagerAdapterMock->expects($this->never())->method('getPackageVersion');

        // Arrange
        $propelFixStep = new PropelFixStep($packageManagerAdapterMock);

        // Act
        $propelFixStep->run(new StepsResponseDto());
    }

    /**
     * @param string $package
     * @param string $version
     * @param array<string, mixed> $composerJson
     *
     * @return \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected function createPackageManagerAdapterMock(string $package, string $version, array $composerJson): PackageManagerAdapterInterface
    {
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter
            ->expects($this->once())
            ->method('getPackageVersion')
            ->with($package)->willReturn($version);

        $packageManagerAdapter
            ->method('getComposerJsonFile')
            ->willReturn($composerJson);

        return $packageManagerAdapter;
    }
}
