<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Response;

use Upgrader\Business\DataProvider\Entity\Collection\ReleaseGroupCollection;

class DataProviderResponse
{
    /**
     * @var \Upgrader\Business\DataProvider\Entity\Collection\ReleaseGroupCollection
     */
    protected $releaseGroupCollection;

    /**
     * @param \Upgrader\Business\DataProvider\Entity\Collection\ReleaseGroupCollection $releaseGroupCollection
     */
    public function __construct(ReleaseGroupCollection $releaseGroupCollection)
    {
        $this->releaseGroupCollection = $releaseGroupCollection;
    }

    /**
     * @return \Upgrader\Business\DataProvider\Entity\Collection\ReleaseGroupCollection
     */
    public function getReleaseGroupCollection(): ReleaseGroupCollection
    {
        return $this->releaseGroupCollection;
    }
}
