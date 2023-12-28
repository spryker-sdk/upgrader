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
use Upgrade\Application\Dto\ReleaseGroupFilterResponseDto;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\ReleaseGroupFilter;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\ReleaseGroupFilterItemInterface;

class ReleaseGroupFilterTest extends TestCase
{
    /**
     * @return void
     */
    public function testFilter(): void
    {
        // Arrange
        $releaseGroupDto = $this->createReleaseGroupDto();
        $filterItem = $this->createMock(ReleaseGroupFilterItemInterface::class);
        $filterItem->method('filter')->willReturn(new ReleaseGroupFilterResponseDto(
            $releaseGroupDto,
            new ModuleDtoCollection([$this->createModuleDto()]),
        ));
        $filter = new ReleaseGroupFilter([$filterItem, $filterItem]);

        // Act
        $filterResponse = $filter->filter($releaseGroupDto);

        // Assert
        $this->assertInstanceOf(ReleaseGroupFilterResponseDto::class, $filterResponse);
        $this->assertSame(2, $filterResponse->getProposedModuleCollection()->count());
    }

    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto
     */
    protected function createReleaseGroupDto(): ReleaseGroupDto
    {
        return new ReleaseGroupDto(
            4821,
            'CC-26540 Introduced the Shipment Types BAPI',
            new ModuleDtoCollection([$this->createModuleDto()]),
            new ModuleDtoCollection(),
            new DateTime(),
            true,
            'https://api.release.spryker.com/release-group/4821',
            100,
        );
    }

    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ModuleDto
     */
    protected function createModuleDto(): ModuleDto
    {
        return new ModuleDto('spryker/shipment-types-backend-api', '0.1.0', 'minor');
    }
}
