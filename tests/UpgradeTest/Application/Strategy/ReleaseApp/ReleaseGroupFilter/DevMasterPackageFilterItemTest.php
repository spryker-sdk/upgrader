<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\ReleaseApp\ReleaseGroupFilter;

use PHPUnit\Framework\TestCase;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\DevMasterPackageFilterItem;

class DevMasterPackageFilterItemTest extends TestCase
{
    /**
     * @return void
     */
    public function testFilterShouldReturnFilteredWhenDevMasterInComposerRequireSection(): void
    {
        // Arrange
        $releaseGroupDto = $this->createReleaseGroupDto([
            new ModuleDto('spryker/package-one', '4.17.0', 'minor'),
            new ModuleDto('spryker/package-two', '3.17.0', 'minor'),
        ]);
        $packageManagerAdapterMock = $this->createPackageManagerAdapterMock(['require' => ['spryker/package-one' => 'dev-master as 4.17.0']]);
        $devMasterPackageFilterItem = new DevMasterPackageFilterItem($packageManagerAdapterMock);

        // Act
        $releaseGroupDto = $devMasterPackageFilterItem->filter($releaseGroupDto);

        // Assert
        $this->assertSame(1, $releaseGroupDto->getModuleCollection()->count());
        $this->assertSame('spryker/package-two', $releaseGroupDto->getModuleCollection()->toArray()[0]->getName());
    }

    /**
     * @return void
     */
    public function testFilterShouldReturnFilteredWhenDevMasterInComposerRequireAndRequireDevSection(): void
    {
        // Arrange
        $releaseGroupDto = $this->createReleaseGroupDto([
            new ModuleDto('spryker/package-one', '4.17.0', 'minor'),
            new ModuleDto('spryker/package-two', '3.17.0', 'minor'),
            new ModuleDto('spryker/package-three', '2.17.0', 'minor'),
        ]);
        $packageManagerAdapterMock = $this->createPackageManagerAdapterMock([
            'require' => ['spryker/package-one' => 'dev-master as 4.17.0'],
            'require-dev' => ['spryker/package-two' => 'dev-master as 3.17.0'],
        ]);
        $devMasterPackageFilterItem = new DevMasterPackageFilterItem($packageManagerAdapterMock);

        // Act
        $releaseGroupDto = $devMasterPackageFilterItem->filter($releaseGroupDto);

        // Assert
        $this->assertSame(1, $releaseGroupDto->getModuleCollection()->count());
        $this->assertSame('spryker/package-three', $releaseGroupDto->getModuleCollection()->toArray()[0]->getName());
    }

    /**
     * @param array<\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto> $moduleDto
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto
     */
    protected function createReleaseGroupDto(array $moduleDto): ReleaseGroupDto
    {
        return new ReleaseGroupDto(
            1,
            'RG1',
            new ModuleDtoCollection($moduleDto),
            false,
            'https://api.release.spryker.com/release-groups/view/1',
            100,
        );
    }

    /**
     * @param array<string, mixed> $composerJson
     *
     * @return \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected function createPackageManagerAdapterMock(array $composerJson = []): PackageManagerAdapterInterface
    {
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->method('getComposerJsonFile')->willReturn($composerJson);

        return $packageManagerAdapter;
    }
}
