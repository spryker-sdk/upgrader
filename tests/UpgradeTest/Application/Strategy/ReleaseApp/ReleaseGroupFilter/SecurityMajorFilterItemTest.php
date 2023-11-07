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
use Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\SecurityMajorFilterItem;

class SecurityMajorFilterItemTest extends TestCase
{
    /**
     * @return void
     */
    public function testShouldFilterMajorPackagesFromSecurityRgs(): void
    {
        // Arrange
        $releaseGroupDto = $this->createReleaseGroupDto();
        $releaseGroupDto->setIsSecurity(true);

        $packageManagerAdapterMock = $this->createPackageManagerAdapterMock([
            ['spryker/package-one', '4.16.0'],
            ['spryker/package-two', '4.0.0'],
        ]);

        $filter = new SecurityMajorFilterItem($packageManagerAdapterMock);

        // Act
        $response = $filter->filter($releaseGroupDto);

        // Assert
        $this->assertSame(2, $response->getReleaseGroupDto()->getModuleCollection()->count());

        $modules = $releaseGroupDto->getModuleCollection()->toArray();

        $this->assertSame('spryker/package-two', $modules[0]->getName());
        $this->assertSame('4.17.0', $modules[0]->getVersion());

        $this->assertSame('spryker/package-three', $modules[1]->getName());
        $this->assertSame('2.17.2', $modules[1]->getVersion());
    }

    /**
     * @return void
     */
    public function testShouldNotFilterMajorPackagesFromNotSecurityRgs(): void
    {
        // Arrange
        $releaseGroupDto = $this->createReleaseGroupDto();
        $releaseGroupDto->setIsSecurity(false);

        $packageManagerAdapterMock = $this->createPackageManagerAdapterMock([
            ['spryker/package-one', '4.16.0'],
            ['spryker/package-two', '4.0.0'],
        ]);

        $filter = new SecurityMajorFilterItem($packageManagerAdapterMock);

        // Act
        $response = $filter->filter($releaseGroupDto);

        // Assert
        $this->assertSame(3, $response->getReleaseGroupDto()->getModuleCollection()->count());
    }

    /**
     * @param array<array<string>> $packageVersionMap
     *
     * @return \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected function createPackageManagerAdapterMock(array $packageVersionMap): PackageManagerAdapterInterface
    {
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);
        $packageManagerAdapter
            ->method('getPackageVersion')
            ->willReturnMap($packageVersionMap);

        return $packageManagerAdapter;
    }

    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto
     */
    protected function createReleaseGroupDto(): ReleaseGroupDto
    {
        $moduleDto = [
            new ModuleDto('spryker/package-one', '5.17.0', 'minor'),
            new ModuleDto('spryker/package-two', '4.17.0', 'minor'),
            new ModuleDto('spryker/package-three', '2.17.2', 'minor'),
        ];

        return new ReleaseGroupDto(
            1,
            'RG1',
            new ModuleDtoCollection($moduleDto),
            new DateTime(),
            false,
            'https://api.release.spryker.com/release-groups/view/1',
            100,
        );
    }
}
