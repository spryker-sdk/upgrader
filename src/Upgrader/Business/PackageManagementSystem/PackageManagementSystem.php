<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem;

use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\ReleaseAppClientInterface;
use Upgrader\Business\PackageManagementSystem\Request\PackageManagementSystemRequestInterface;
use Upgrader\Business\PackageManagementSystem\Response\PackageManagementSystemResponse;

class PackageManagementSystem implements PackageManagementSystemInterface
{
    /**
     * @var \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\ReleaseAppClientInterface
     */
    protected $releaseAppClient;

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\ReleaseAppClientInterface $releaseAppClient
     */
    public function __construct(ReleaseAppClientInterface $releaseAppClient)
    {
        $this->releaseAppClient = $releaseAppClient;
    }

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Request\PackageManagementSystemRequestInterface $request
     *
     * @return \Upgrader\Business\PackageManagementSystem\Response\PackageManagementSystemResponse
     */
    public function getNotInstalledReleaseGroupList(PackageManagementSystemRequestInterface $request): PackageManagementSystemResponse
    {
        return $this->releaseAppClient->getNotInstalledReleaseGroupList($request);
    }
}
