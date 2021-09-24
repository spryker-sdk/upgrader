<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Validator\Package;

use Upgrader\Business\Exception\UpgraderException;
use Upgrader\Business\PackageManager\Entity\Package;
use Upgrader\Business\PackageManager\PackageManagerInterface;

class AlreadyInstalledValidator implements PackageValidatorInterface
{
    /**
     * @var \Upgrader\Business\PackageManager\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @param \Upgrader\Business\PackageManager\PackageManagerInterface $packageManager
     */
    public function __construct(PackageManagerInterface $packageManager)
    {
        $this->packageManager = $packageManager;
    }

    /**
     * @param \Upgrader\Business\PackageManager\Entity\Package $package
     *
     * @throws \Upgrader\Business\Exception\UpgraderException
     *
     * @return void
     */
    public function validate(Package $package): void
    {
        $installedVersion = (string)$this->packageManager->getPackageVersion($package->getName());
        if (version_compare($installedVersion, $package->getVersion(), '>=')) {
            $message = sprintf(
                'Package %s:%s already installed.',
                $package->getName(),
                $package->getVersion()
            );

            throw new UpgraderException($message);
        }
    }
}
