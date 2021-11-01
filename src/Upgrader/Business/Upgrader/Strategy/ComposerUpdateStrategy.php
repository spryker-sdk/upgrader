<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Strategy;

use Upgrader\Business\PackageManager\PackageManagerInterface;
use Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection;

class ComposerUpdateStrategy implements UpgradeStrategyInterface
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
     * @return \Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection
     */
    public function upgrade(): UpgraderResponseCollection
    {
        $responses = new UpgraderResponseCollection();

        $result = $this->packageManager->update();
        $responses->add($result);

        return $responses;
    }
}
