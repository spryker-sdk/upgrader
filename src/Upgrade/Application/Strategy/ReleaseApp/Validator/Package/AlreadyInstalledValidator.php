<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Validator\Package;

use PackageManager\Application\Service\PackageManagerServiceInterface;
use PackageManager\Domain\Dto\PackageDto;
use Upgrade\Application\Exception\UpgraderException;

class AlreadyInstalledValidator implements PackageValidatorInterface
{
    /**
     * @var \PackageManager\Application\Service\PackageManagerServiceInterface
     */
    protected $packageManager;

    /**
     * @param \PackageManager\Application\Service\PackageManagerServiceInterface $packageManager
     */
    public function __construct(PackageManagerServiceInterface $packageManager)
    {
        $this->packageManager = $packageManager;
    }

    /**
     * @param \PackageManager\Domain\Dto\PackageDto $package
     *
     * @return void
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
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
