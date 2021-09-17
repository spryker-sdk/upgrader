<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
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
