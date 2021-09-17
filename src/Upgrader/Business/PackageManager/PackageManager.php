<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\PackageManager;

use Upgrader\Business\Command\Response\CommandResponse;
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
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function update(): CommandResponse
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
     * @param \Upgrader\Business\PackageManager\Entity\Collection\PackageCollectionInterface $packageCollection
     *
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function require(PackageCollectionInterface $packageCollection): CommandResponse
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
