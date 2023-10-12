<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Dto\ReleaseGroupFilterResponseDto;

class ReleaseGroupFilter implements ReleaseGroupFilterInterface
{
    /**
     * @var array<\Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\ReleaseGroupFilterItemInterface>
     */
    protected array $releaseGroupFilterItems;

    /**
     * @param array<\Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\ReleaseGroupFilterItemInterface> $releaseGroupFilterItems
     */
    public function __construct(array $releaseGroupFilterItems)
    {
        $this->releaseGroupFilterItems = $releaseGroupFilterItems;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroupDto
     *
     * @return \Upgrade\Application\Dto\ReleaseGroupFilterResponseDto
     */
    public function filter(ReleaseGroupDto $releaseGroupDto): ReleaseGroupFilterResponseDto
    {
        $proposedModuleCollection = new ModuleDtoCollection();

        foreach ($this->releaseGroupFilterItems as $releaseGroupFilterItem) {
            $filterResponse = $releaseGroupFilterItem->filter($releaseGroupDto);
            $releaseGroupDto = $filterResponse->getReleaseGroupDto();
            $proposedModuleCollection->addCollection($filterResponse->getProposedModuleCollection());
        }

        return new ReleaseGroupFilterResponseDto($releaseGroupDto, $proposedModuleCollection);
    }
}
