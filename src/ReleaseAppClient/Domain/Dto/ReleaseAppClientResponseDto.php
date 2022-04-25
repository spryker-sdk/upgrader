<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Dto;

use ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection;

class ReleaseAppClientResponseDto
{
    /**
     * @var \ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection
     */
    protected $releaseGroupCollection;

    /**
     * @param \ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection $releaseGroupCollection
     */
    public function __construct(ReleaseGroupDtoCollection $releaseGroupCollection)
    {
        $this->releaseGroupCollection = $releaseGroupCollection;
    }

    /**
     * @return \ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection
     */
    public function getReleaseGroupCollection(): ReleaseGroupDtoCollection
    {
        return $this->releaseGroupCollection;
    }
}
