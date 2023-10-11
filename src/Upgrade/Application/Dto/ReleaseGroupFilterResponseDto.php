<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Dto;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;

class ReleaseGroupFilterResponseDto
{
    /**
     * @var \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto
     */
    protected ReleaseGroupDto $releaseGroupDto;

    /**
     * @var \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection
     */
    protected ModuleDtoCollection $filteredModuleCollection;

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroupDto
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection|null $filteredModuleCollection
     */
    public function __construct(ReleaseGroupDto $releaseGroupDto, ?ModuleDtoCollection $filteredModuleCollection = null)
    {
        $this->releaseGroupDto = $releaseGroupDto;
        $this->filteredModuleCollection = $filteredModuleCollection ?? new ModuleDtoCollection();
    }

    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto
     */
    public function getReleaseGroupDto(): ReleaseGroupDto
    {
        return $this->releaseGroupDto;
    }

    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection
     */
    public function getFilteredModuleCollection(): ModuleDtoCollection
    {
        return $this->filteredModuleCollection;
    }
}
