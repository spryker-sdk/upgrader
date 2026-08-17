<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\ReleaseApp\ReleaseGroupFilter;

use DateTime;
use PHPUnit\Framework\TestCase;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\DevMasterPackageFilterItem;

class DevMasterPackageFilterItemTest extends TestCase
{
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
        $response = $devMasterPackageFilterItem->filter($releaseGroupDto);

        // Assert
        $this->assertSame(1, $response->getReleaseGroupDto()->getModuleCollection()->count());
        $this->assertSame('spryker/package-two', $response->getReleaseGroupDto()->getModuleCollection()->toArray()[0]->getName());
    }

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
        $response = $devMasterPackageFilterItem->filter($releaseGroupDto);

    // Assert
        $this->assertSame(1, $response->getReleaseGroupDto()->getModuleCollection()->count());
        $this->assertSame('spryker/package-three', $response->getReleaseGroupDto()->getModuleCollection()->toArray()[0]->getName());
    }

    /**
     * @param array<\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto> $moduleDto
     */
    protected function createReleaseGroupDto(array $moduleDto): ReleaseGroupDto
    {
        return new ReleaseGroupDto(
            1,
            'RG1',
            new ModuleDtoCollection($moduleDto),
            new ModuleDtoCollection(),
            new ModuleDtoCollection(),
            new DateTime(),
            false,
            'https://api.release.spryker.com/release-groups/view/1',
            100,
        );
    }

    /**
     * @param array<string, mixed> $composerJson
     */
    protected function createPackageManagerAdapterMock(array $composerJson = []): PackageManagerAdapterInterface
    {
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->method('getComposerJsonFile')->willReturn($composerJson);

        return $packageManagerAdapter;
    }
}
