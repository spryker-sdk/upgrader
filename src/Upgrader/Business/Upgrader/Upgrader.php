<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

use Upgrader\Business\Command\Response\Collection\CommandResponseCollection;
use Upgrader\Business\Upgrader\Manager\DataProviderManager;
use Upgrader\Business\Upgrader\Manager\ReleaseGroupManagerInterface;

class Upgrader implements UpgraderInterface
{
    /**
     * @var \Upgrader\Business\Upgrader\Manager\ReleaseGroupManagerInterface
     */
    protected $releaseGroupManager;

    /**
     * @var \Upgrader\Business\Upgrader\Manager\DataProviderManager
     */
    protected $dataProviderManager;

    /**
     * @param Manager\ReleaseGroupManagerInterface $releaseGroupManager
     * @param Manager\DataProviderManager $dataProviderManager
     */
    public function __construct(
        ReleaseGroupManagerInterface $releaseGroupManager,
        DataProviderManager $dataProviderManager
    ) {
        $this->releaseGroupManager = $releaseGroupManager;
        $this->dataProviderManager = $dataProviderManager;
    }

    /**
     * @return \Upgrader\Business\Command\Response\Collection\CommandResponseCollection
     */
    public function upgrade(): CommandResponseCollection
    {
        $dataProviderResponse = $this->dataProviderManager->getNotInstalledReleaseGroupList();

        return $this->releaseGroupManager->requireCollection($dataProviderResponse->getReleaseGroupCollection());
    }
}
