<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

use Upgrader\Business\Command\ResultOutput\CommandResultOutput;
use Upgrader\Business\PackageManager\PackageManagerInterface;
use Upgrader\Business\VersionControlSystem\VersionControlSystemInterface;

class Upgrader implements UpgraderInterface
{
    /**
     * @var \Upgrader\Business\PackageManager\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @var \Upgrader\Business\VersionControlSystem\VersionControlSystemInterface
     */
    protected $versionControlSystem;

    /**
     * @param \Upgrader\Business\PackageManager\PackageManagerInterface $packageManager
     * @param \Upgrader\Business\VersionControlSystem\VersionControlSystemInterface $versionControlSystem
     */
    public function __construct(
        PackageManagerInterface $packageManager,
        VersionControlSystemInterface $versionControlSystem
    ) {
        $this->packageManager = $packageManager;
        $this->versionControlSystem = $versionControlSystem;
    }

    /**
     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput
     */
    public function upgrade(): CommandResultOutput
    {
        $commandResultOutput = $this->versionControlSystem->checkUncommittedChanges();

        if (!$commandResultOutput->isSuccess()) {
            return $commandResultOutput;
        }

        return $this->packageManager->update();
    }
}
