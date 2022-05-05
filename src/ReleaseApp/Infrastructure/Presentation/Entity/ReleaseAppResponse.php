<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Presentation\Entity;

use ReleaseApp\Infrastructure\Presentation\Entity\Collection\ReleaseGroupDtoCollection;

class ReleaseAppResponse
{
    /**
     * @var \ReleaseApp\Infrastructure\Presentation\Entity\Collection\ReleaseGroupDtoCollection
     */
    protected $releaseGroupCollection;

    /**
     * @param \ReleaseApp\Infrastructure\Presentation\Entity\Collection\ReleaseGroupDtoCollection $releaseGroupCollection
     */
    public function __construct(ReleaseGroupDtoCollection $releaseGroupCollection)
    {
        $this->releaseGroupCollection = $releaseGroupCollection;
    }

    /**
     * @return \ReleaseApp\Infrastructure\Presentation\Entity\Collection\ReleaseGroupDtoCollection
     */
    public function getReleaseGroupCollection(): ReleaseGroupDtoCollection
    {
        return $this->releaseGroupCollection;
    }
}
