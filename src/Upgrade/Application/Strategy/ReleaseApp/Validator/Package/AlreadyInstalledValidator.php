<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Validator\Package;

use Upgrade\Application\Bridge\PackageManagerBridgeInterface;
use Upgrade\Application\Exception\UpgraderException;
use Upgrade\Domain\Entity\Package;

class AlreadyInstalledValidator implements PackageValidatorInterface
{
    /**
     * @var \Upgrade\Application\Bridge\PackageManagerBridgeInterface
     */
    protected PackageManagerBridgeInterface $composerClient;

    /**
     * @param \Upgrade\Application\Bridge\PackageManagerBridgeInterface $composerClient
     */
    public function __construct(PackageManagerBridgeInterface $composerClient)
    {
        $this->composerClient = $composerClient;
    }

    /**
     * @param \Upgrade\Domain\Entity\Package $package
     *
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return void
     */
    public function validate(Package $package): void
    {
        $installedVersion = (string)$this->composerClient->getPackageVersion($package->getName());
        if (version_compare($installedVersion, $package->getVersion(), '>=')) {
            $message = sprintf(
                'Package %s:%s already installed.',
                $package->getName(),
                $package->getVersion(),
            );

            throw new UpgraderException($message);
        }
    }
}
