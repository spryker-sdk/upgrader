<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager;

use Upgrader\Business\Command\ResultOutput\CommandResultOutput;
use Upgrader\Business\PackageManager\Client\PackageManagerClientInterface;
use Upgrader\Business\PackageManager\Entity\Collection\PackageCollectionInterface;

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

    /**
     * @return string
     */
    public function getProjectName(): string
    {
        return $this->packageManagerClient->getProjectName();
    }

    public function getComposerJsonFile(): array
    {
        return $this->packageManagerClient->getComposerJsonFile();
    }

    public function getComposerLockFile(): array
    {
        return $this->packageManagerClient->getComposerLockFile();
    }

    public function require(PackageCollectionInterface $packageCollection): CommandResultOutput
    {
        return $this->packageManagerClient->require($packageCollection);
    }

    public function getPackageVersion(string $packageName): ?string
    {
        return $this->packageManagerClient->getPackageVersion($packageName);
    }
}
