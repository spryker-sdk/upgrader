<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager;

use Upgrader\Business\PackageManager\Client\PackageManagerClientInterface;
use Upgrader\Business\PackageManager\Response\PackageManagerResponse;
use Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection;

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
     * @return string
     */
    public function getProjectName(): string
    {
        return $this->packageManagerClient->getProjectName();
    }

    /**
     * @return array
     */
    public function getComposerJsonFile(): array
    {
        return $this->packageManagerClient->getComposerJsonFile();
    }

    /**
     * @return array
     */
    public function getComposerLockFile(): array
    {
        return $this->packageManagerClient->getComposerLockFile();
    }

    /**
     * @param \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection $packageCollection
     *
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    public function require(PackageTransferCollection $packageCollection): PackageManagerResponse
    {
        return $this->packageManagerClient->require($packageCollection);
    }

    /**
     * @param string $packageName
     *
     * @return string|null
     */
    public function getPackageVersion(string $packageName): ?string
    {
        return $this->packageManagerClient->getPackageVersion($packageName);
    }
}
