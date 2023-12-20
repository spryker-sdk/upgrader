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
use Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\DummyPackageFilterItem;

class DummyPackageFilterItemTest extends TestCase
{
    /**
     * @dataProvider dummyPackagesDataProvider
     *
     * @param string $packageName
     * @param bool $isDummy
     *
     * @return void
     */
    public function testFilterShouldFilterDummyPackages(string $packageName, bool $isDummy): void
    {
        // Arrange
        $releaseGroupDto = $this->createReleaseGroupDto([
            new ModuleDto($packageName, '1.1.0', 'minor'),
        ]);

        $dummyPackageFilterItem = new DummyPackageFilterItem();

        // Act
        $response = $dummyPackageFilterItem->filter($releaseGroupDto);

        // Assert
        $modules = $response->getReleaseGroupDto()->getModuleCollection()->toArray();

        if ($isDummy) {
            $this->assertEmpty($modules);

            return;
        }

        $this->assertCount(1, $modules);
        $this->assertSame($packageName, $modules[0]->getName());
    }

    /**
     * @return array<array<mixed>>
     */
    public function dummyPackagesDataProvider(): array
    {
        return [
            ['spryker/dummy-merchant-portal-gui', true],
            ['spryker-shop/dummy-merchant-portal-gui', true],
            ['spryker/merchant-portal-gui-example', true],
            ['spryker-shop/merchant-portal-gui-example', true],
            ['non-spryker/dummy-merchant-portal-gui', false],
            ['non-spryker/merchant-portal-gui-example', false],
            ['spryker/dummy', false],
            ['spryker-shop/dummy', false],
            ['spryker/example', false],
            ['spryker-shop/example', false],
            ['spryker-eco/authorization-picking-app-backend-api', false],
        ];
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
            new ModuleDtoCollection(),
            new DateTime(),
            false,
            'https://api.release.spryker.com/release-groups/view/1',
            100,
        );
    }
}
