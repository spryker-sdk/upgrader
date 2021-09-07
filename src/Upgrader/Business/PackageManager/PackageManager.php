<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager;

use Upgrader\Business\Command\ResultOutput\CommandResultOutput;
use Upgrader\Business\PackageManager\Client\PackageManagerClientInterface;

class PackageManager implements PackageManagerInterface
{
    /**
     * @var \Upgrader\Business\PackageManager\Client\PackageManagerClientInterface
     */
    protected $packageManagerClient;

    /**
     * @param \Upgrader\Business\PackageManager\Client\PackageManagerClientInterface $packageManagerClient
     */
    public function __construct(PackageManagerClientInterface $packageManagerClient)
    {
        $this->packageManagerClient = $packageManagerClient;
    }

    /**
     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput
     */
    public function update(): CommandResultOutput
    {
        return $this->packageManagerClient->runUpdate();
    }
}
