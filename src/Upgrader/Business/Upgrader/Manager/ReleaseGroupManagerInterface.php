<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Manager;

use Upgrader\Business\Command\Response\Collection\CommandResponseCollection;
use Upgrader\Business\Command\Response\CommandResponse;
use Upgrader\Business\DataProvider\Entity\Collection\ReleaseGroupCollection;
use Upgrader\Business\DataProvider\Entity\ReleaseGroup;

interface ReleaseGroupManagerInterface
{
    /**
     * @param \Upgrader\Business\DataProvider\Entity\Collection\ReleaseGroupCollection $releaseGroupCollection
     *
     * @return \Upgrader\Business\Command\Response\Collection\CommandResponseCollection
     */
    public function requireCollection(ReleaseGroupCollection $releaseGroupCollection): CommandResponseCollection;

    /**
     * @param \Upgrader\Business\DataProvider\Entity\ReleaseGroup $releaseGroup
     *
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function require(ReleaseGroup $releaseGroup): CommandResponse;
}
