<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\ReleaseApp\Processor\PreRequiredProcessor;

use PHPUnit\Framework\TestCase;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\PreRequiredProcessor\PropelProcessorStrategy;

class PropelProcessorStrategyTest extends TestCase
{
    /**
     * @return void
     */
    public function testProcessShouldAddPropelPackageWhenItHasSpecificVersion(): void
    {
        // Arrange
        $packageManagerAdapterMock = $this->createPackageManagerAdapterMock(
            PropelProcessorStrategy::PACKAGE_NAME,
            PropelProcessorStrategy::LOCK_PACKAGE_VERSION,
            ['require' => []],
        );

        $releaseGroupDtoCollection = new ReleaseGroupDtoCollection([$this->createReleaseGroupDto([
            new ModuleDto('spryker/package-one', '4.17.0', 'minor'),
        ])]);

        $propelProcessorStrategy = new PropelProcessorStrategy($packageManagerAdapterMock);

        // Act
        $releaseGroupDtoCollection = $propelProcessorStrategy->process($releaseGroupDtoCollection);

        // Assert
        $this->assertSame(2, $releaseGroupDtoCollection->count());

        $propelModule = $releaseGroupDtoCollection->toArray()[1]->getModuleCollection()->toArray()[0];

        $this->assertSame(PropelProcessorStrategy::PACKAGE_NAME, $propelModule->getName());
        $this->assertSame(PropelProcessorStrategy::LOCK_PACKAGE_VERSION, $propelModule->getVersion());
    }

    /**
     * @return void
     */
    public function testProcessShouldSkipAddingPropelPackageWhenPackageInComposerRequire(): void
    {
        // Arrange
        $packageManagerAdapterMock = $this->createPackageManagerAdapterMock(
            PropelProcessorStrategy::PACKAGE_NAME,
            PropelProcessorStrategy::LOCK_PACKAGE_VERSION,
            ['require' => [PropelProcessorStrategy::PACKAGE_NAME => '1.0.0']],
        );

        $releaseGroupDtoCollection = new ReleaseGroupDtoCollection([$this->createReleaseGroupDto([
            new ModuleDto('spryker/package-one', '4.17.0', 'minor'),
        ])]);

        $propelProcessorStrategy = new PropelProcessorStrategy($packageManagerAdapterMock);

        // Act
        $releaseGroupDtoCollection = $propelProcessorStrategy->process($releaseGroupDtoCollection);

        // Assert
        $this->assertSame(1, $releaseGroupDtoCollection->count());

        $propelModule = $releaseGroupDtoCollection->toArray()[0]->getModuleCollection()->toArray()[0];

        $this->assertSame('spryker/package-one', $propelModule->getName());
        $this->assertSame('4.17.0', $propelModule->getVersion());
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

    /**
     * @param array<\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto> $moduleDto
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto
     */
    protected function createReleaseGroupDto(array $moduleDto): ReleaseGroupDto
    {
        return new ReleaseGroupDto(
            'RG1',
            new ModuleDtoCollection($moduleDto),
            false,
            'https://api.release.spryker.com/release-groups/view/1',
        );
    }
}
