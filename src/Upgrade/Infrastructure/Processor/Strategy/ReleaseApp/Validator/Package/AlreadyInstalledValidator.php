<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Validator\Package;

use Upgrade\Application\Dto\PackageManager\PackageDto;
use Upgrade\Infrastructure\Exception\UpgraderException;
use Upgrade\Infrastructure\PackageManager\PackageManagerInterface;

class AlreadyInstalledValidator implements PackageValidatorInterface
{
    /**
     * @var \Upgrade\Infrastructure\PackageManager\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @param \Upgrade\Infrastructure\PackageManager\PackageManagerInterface $packageManager
     */
    public function __construct(PackageManagerInterface $packageManager)
    {
        $this->packageManager = $packageManager;
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManager\PackageDto $package
     *
     * @throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     * @return void
     */
    public function validate(PackageDto $package): void
    {
        $installedVersion = (string)$this->packageManager->getPackageVersion($package->getName());
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
