<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\Package;

use PackageManager\Domain\Dto\PackageDto;
use Upgrade\Infrastructure\Exception\UpgraderException;
use PackageManager\Application\Service\PackageManagerInterface;

class AlreadyInstalledValidator implements PackageValidatorInterface
{
    /**
     * @var \PackageManager\Application\Service\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @param \PackageManager\Application\Service\PackageManagerInterface $packageManager
     */
    public function __construct(PackageManagerInterface $packageManager)
    {
        $this->packageManager = $packageManager;
    }

    /**
     * @param \PackageManager\Domain\Dto\PackageDto $package
     *
     * @return void
     *@throws \Upgrade\Infrastructure\Exception\UpgraderException
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
