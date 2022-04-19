<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManagementSystem;

use Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemRequestDto;
use Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemResponseDto;
use Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\ReleaseAppClientInterface;

class PackageManagementSystem implements PackageManagementSystemInterface
{
    /**
     * @var \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\ReleaseAppClientInterface
     */
    protected $releaseAppClient;

    /**
     * @param \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\ReleaseAppClientInterface $releaseAppClient
     */
    public function __construct(ReleaseAppClientInterface $releaseAppClient)
    {
        $this->releaseAppClient = $releaseAppClient;
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemRequestDto $request
     *
     * @return \Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemResponseDto
     */
    public function getNotInstalledReleaseGroupList(PackageManagementSystemRequestDto $request): PackageManagementSystemResponseDto
    {
        return $this->releaseAppClient->getNotInstalledReleaseGroupList($request);
    }
}
