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
    protected ReleaseGroupDto $releaseGroupDto;

    protected ModuleDtoCollection $proposedModuleCollection;

    public function __construct(ReleaseGroupDto $releaseGroupDto, ?ModuleDtoCollection $proposedModuleCollection = null)
    {
        $this->releaseGroupDto = $releaseGroupDto;
        $this->proposedModuleCollection = $proposedModuleCollection ?? new ModuleDtoCollection();
    }

    public function getReleaseGroupDto(): ReleaseGroupDto
    {
        return $this->releaseGroupDto;
    }

    public function getProposedModuleCollection(): ModuleDtoCollection
    {
        return $this->proposedModuleCollection;
    }
}
