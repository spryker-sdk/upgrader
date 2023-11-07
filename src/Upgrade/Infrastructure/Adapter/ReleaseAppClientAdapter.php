<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Adapter;

use ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use ReleaseApp\Domain\Client\Request\UpgradeReleaseGroupInstructionsRequest;
use ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Adapter\ReleaseAppClientAdapterInterface;

class ReleaseAppClientAdapter implements ReleaseAppClientAdapterInterface
{
    /**
     * @var \ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface
     */
    protected ReleaseAppServiceInterface $releaseApp;

    /**
     * @var \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManager;

    /**
     * @param \ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface $releaseApp
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     */
    public function __construct(ReleaseAppServiceInterface $releaseApp, PackageManagerAdapterInterface $packageManager)
    {
        $this->releaseApp = $releaseApp;
        $this->packageManager = $packageManager;
    }

    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse
     */
    public function getNewReleaseGroups(): ReleaseAppResponse
    {
        $upgradeInstructionsRequest = $this->createDataProviderRequest();

        return $this->releaseApp->getNewReleaseGroups($upgradeInstructionsRequest);
    }

    /**
     * @param int $releaseGroupId
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse
     */
    public function getReleaseGroup(int $releaseGroupId): ReleaseAppResponse
    {
        return $this->releaseApp->getReleaseGroup(new UpgradeReleaseGroupInstructionsRequest($releaseGroupId));
    }

    /**
     * @return \ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest
     */
    protected function createDataProviderRequest(): UpgradeInstructionsRequest
    {
        $packages = $this->extractLockedPackages($this->packageManager->getComposerLockFile());

        return new UpgradeInstructionsRequest($packages);
    }

    /**
     * @param array<string, mixed> $composerLockArray
     *
     * @return array<string, string>
     */
    protected function extractLockedPackages(array $composerLockArray): array
    {
        if (empty($composerLockArray['packages'])) {
            return [];
        }

        $locked = [];

        foreach ($composerLockArray['packages'] as $package) {
            $name = (string)$package['name'];
            if (!preg_match('#^spryker.*(?<!feature)/#', $name)) {
                continue;
            }

            $locked[$name] = (string)$package['version'];
        }

        return $locked;
    }
}
