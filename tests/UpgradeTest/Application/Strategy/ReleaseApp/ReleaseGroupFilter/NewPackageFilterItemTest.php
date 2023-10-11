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
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\NewPackageFilterItem;

/**
 * @group test1
 */
class NewPackageFilterItemTest extends TestCase
{
    /**
     * @return void
     */
    public function testFilterShouldNotFilterNewPackages(): void
    {
        // Arrange
        $releaseGroupDto = $this->createReleaseGroupDto([
            new ModuleDto('spryker/package-one', '4.17.0', 'minor'),
            new ModuleDto('spryker/package-two', '3.17.0', 'minor'),
            new ModuleDto('spryker/package-three', '2.17.0', 'minor'),
        ]);

        $packageManagerAdapterMock = $this->createPackageManagerAdapterMock([
            ['spryker/package-one', '4.16.0'],
            ['spryker/package-two', null],
            ['spryker/package-three', '2.16.0'],
        ]);
        $configurationProviderMock = $this->createConfigurationProviderMock();
        $configurationProviderMock
            ->method('isPackageUpgradeOnly')
            ->willReturn(false);

        $filter = new NewPackageFilterItem($packageManagerAdapterMock, $configurationProviderMock);

        // Act
        $response = $filter->filter($releaseGroupDto);

        // Assert
        $this->assertSame(3, $response->getReleaseGroupDto()->getModuleCollection()->count());
        $this->assertTrue($response->getProposedModuleCollection()->isEmpty());
    }

    /**
     * @return void
     */
    public function testFilterShouldFilterNewPackages(): void
    {
        // Arrange
        $releaseGroupDto = $this->createReleaseGroupDto([
            new ModuleDto('spryker/package-one', '4.17.0', 'minor'),
            new ModuleDto('spryker/package-two', '3.17.0', 'minor'),
            new ModuleDto('spryker/package-three', '2.17.0', 'minor'),
        ]);

        $packageManagerAdapterMock = $this->createPackageManagerAdapterMock([
            ['spryker/package-one', '4.16.0'],
            ['spryker/package-two', null],
            ['spryker/package-three', '2.16.0'],
        ]);
        $configurationProviderMock = $this->createConfigurationProviderMock();
        $configurationProviderMock
            ->method('isPackageUpgradeOnly')
            ->willReturn(true);
        $filter = new NewPackageFilterItem($packageManagerAdapterMock, $configurationProviderMock);

        // Act
        $response = $filter->filter($releaseGroupDto);

        // Assert
        $this->assertSame(2, $response->getReleaseGroupDto()->getModuleCollection()->count());

        $modules = $response->getReleaseGroupDto()->getModuleCollection()->toArray();

        $this->assertSame('spryker/package-one', $modules[0]->getName());
        $this->assertSame('4.17.0', $modules[0]->getVersion());

        $this->assertSame('spryker/package-three', $modules[1]->getName());
        $this->assertSame('2.17.0', $modules[1]->getVersion());

        $this->assertSame(1, $response->getProposedModuleCollection()->count());
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected function createConfigurationProviderMock(): ConfigurationProviderInterface
    {
        return $this->createMock(ConfigurationProviderInterface::class);
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
}
