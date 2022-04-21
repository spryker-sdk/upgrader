<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Dto\ReleaseAppClient;

use Upgrade\Application\Dto\ReleaseAppClient\Collection\ReleaseGroupDtoCollection;

class ReleaseAppClientResponseDto
{
    /**
     * @var \Upgrade\Application\Dto\ReleaseAppClient\Collection\ReleaseGroupDtoCollection
     */
    protected $releaseGroupCollection;

    /**
     * @param \Upgrade\Application\Dto\ReleaseAppClient\Collection\ReleaseGroupDtoCollection $releaseGroupCollection
     */
    public function __construct(ReleaseGroupDtoCollection $releaseGroupCollection)
    {
        $this->releaseGroupCollection = $releaseGroupCollection;
    }

    /**
     * @return \Upgrade\Application\Dto\ReleaseAppClient\Collection\ReleaseGroupDtoCollection
     */
    public function getReleaseGroupCollection(): ReleaseGroupDtoCollection
    {
        return $this->releaseGroupCollection;
    }
}
