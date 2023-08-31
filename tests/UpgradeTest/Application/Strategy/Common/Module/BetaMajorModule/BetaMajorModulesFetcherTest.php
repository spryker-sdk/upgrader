<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\Common\Module\BetaMajorModule;

use PHPUnit\Framework\TestCase;
use ReleaseApp\Application\Configuration\ReleaseAppConstant;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Strategy\Common\Module\BetaMajorModule\BetaMajorModulesFetcher;

class BetaMajorModulesFetcherTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetBetaMajorsNotInstalledInDevShouldFilterLockDevPackages(): void
    {
        //Arrange
        $packageManagerAdapterMock = $this->createPackageManagerAdapterMock(true);
        $betaMajorModulesFetcher = new BetaMajorModulesFetcher($packageManagerAdapterMock);
        $moduleDtoCollection = new ModuleDtoCollection([new ModuleDto('spryker/picking-lists-backend-api', '0.1.1', ReleaseAppConstant::MODULE_TYPE_MINOR)]);

        //Act
        $fetchedModuleDtoCollection = $betaMajorModulesFetcher->getBetaMajorsNotInstalledInDev($moduleDtoCollection);

        //Assert
        $this->assertEmpty($fetchedModuleDtoCollection);
    }

    /**
     * @return void
     */
    public function testGetBetaMajorsNotInstalledInDevShouldBetaMajors(): void
    {
        //Arrange
        $packageManagerAdapterMock = $this->createPackageManagerAdapterMock(false);
        $betaMajorModulesFetcher = new BetaMajorModulesFetcher($packageManagerAdapterMock);
        $moduleDtoCollection = new ModuleDtoCollection([new ModuleDto('spryker/picking-lists-backend-api', '0.1.1', ReleaseAppConstant::MODULE_TYPE_MINOR)]);

        //Act
        $fetchedModuleDtoCollection = $betaMajorModulesFetcher->getBetaMajorsNotInstalledInDev($moduleDtoCollection);

        //Assert
        $this->assertCount(1, $fetchedModuleDtoCollection);
    }

    /**
     * @param bool $isLockDevPackageVersion
     *
     * @return \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected function createPackageManagerAdapterMock(bool $isLockDevPackageVersion): PackageManagerAdapterInterface
    {
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter->method('isLockDevPackage')->willReturn($isLockDevPackageVersion);

        return $packageManagerAdapter;
    }
}
