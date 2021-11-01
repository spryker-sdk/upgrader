<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager;

use Upgrader\Business\PackageManager\Client\ComposerClientInterface;
use Upgrader\Business\PackageManager\Client\ComposerLockDiffClientInterface;
use Upgrader\Business\PackageManager\Response\Collection\PackageManagerResponseCollection;
use Upgrader\Business\PackageManager\Response\PackageManagerResponse;
use Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection;

class PackageManager implements PackageManagerInterface
{
    /**
     * @var \Upgrader\Business\PackageManager\Client\ComposerClientInterface
     */
    protected $packageManagerClient;

    /**
     * @var \Upgrader\Business\PackageManager\Client\ComposerLockDiffClientInterface
     */
    protected $composerLockDiffClient;

    /**
     * @param \Upgrader\Business\PackageManager\Client\ComposerClientInterface $packageManagerClient
     * @param \Upgrader\Business\PackageManager\Client\ComposerLockDiffClientInterface $composerLockDiffClient
     */
    public function __construct(
        ComposerClientInterface $packageManagerClient,
        ComposerLockDiffClientInterface $composerLockDiffClient
    ) {
        $this->packageManagerClient = $packageManagerClient;
        $this->composerLockDiffClient = $composerLockDiffClient;
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
     * @param \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection $packageCollection
     *
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    public function requireDev(PackageTransferCollection $packageCollection): PackageManagerResponse
    {
        return $this->packageManagerClient->requireDev($packageCollection);
    }

    /**
     * @return \Upgrader\Business\PackageManager\Response\Collection\PackageManagerResponseCollection
     */
    public function update(): PackageManagerResponseCollection
    {
        $rawResponse = $this->packageManagerClient->update();

        if ($rawResponse->isSuccess()) {
            $updateResponseCollection = new PackageManagerResponseCollection();
            $composerLockDiffResponseCollection = $this->composerLockDiffClient->getComposerLockDiff();

            foreach ($composerLockDiffResponseCollection as $diffResponse) {
                $updateResponse = new PackageManagerResponse(
                    true,
                    $rawResponse->getOutput(),
                    $diffResponse->getPackageList(),
                );
                $updateResponseCollection->add($updateResponse);
            }

            return $updateResponseCollection;
        }

        return new PackageManagerResponseCollection([$rawResponse]);
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

    /**
     * @param string $packageName
     *
     * @return bool
     */
    public function isDevPackage(string $packageName): bool
    {
        return $this->packageManagerClient->isDevPackage($packageName);
    }
}
