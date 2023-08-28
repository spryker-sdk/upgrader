<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\ReleaseApp\ReleaseGroupFilter;

use PHPUnit\Framework\TestCase;
use ReleaseApp\Application\Configuration\ReleaseAppConstant;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\BetaMajorPackageFilterItem;

class BetaMajorPackageFilterItemTest extends TestCase
{
    /**
     * @return void
     */
    public function testFilterShouldFilterBetaMajors(): void
    {
        // Arrange
        $moduleOne = 'spryker/one';
        $moduleTwo = 'spryker/two';
        $moduleThree = 'spryker/three';

        $modules = [
            new ModuleDto($moduleOne, '0.2.0', ReleaseAppConstant::MODULE_TYPE_MINOR),
            new ModuleDto($moduleTwo, '1.2.0', ReleaseAppConstant::MODULE_TYPE_MAJOR),
            new ModuleDto($moduleThree, '0.2.0', ReleaseAppConstant::MODULE_TYPE_MINOR),
        ];

        $releaseGroupDto = new ReleaseGroupDto(
            'RG',
            new ModuleDtoCollection($modules),
            true,
            '',
            false,
        );

        $modulesVersions = [
            [$moduleOne, null],
            [$moduleTwo, '1.1.1'],
            [$moduleThree, '0.1.1'],
        ];

        $packageManagerAdapterMock = $this->createPackageManagerAdapterMock($modulesVersions);

        $betaMajorPackageFilterItem = new BetaMajorPackageFilterItem($packageManagerAdapterMock);

        // Act
        $releaseGroupDto = $betaMajorPackageFilterItem->filter($releaseGroupDto);

        // Assert
        $this->assertSame(2, $releaseGroupDto->getModuleCollection()->count());
        $this->assertSame([$modules[1]], $releaseGroupDto->getModuleCollection()->getMajors());
        $this->assertSame([$modules[2]], $releaseGroupDto->getModuleCollection()->getBetaMajors());
    }

    /**
     * @param array<array<string>> $returnMap
     *
     * @return \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected function createPackageManagerAdapterMock(array $returnMap): PackageManagerAdapterInterface
    {
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->method('getPackageVersion')->willReturnMap($returnMap);

        return $packageManagerAdapter;
    }
}
