<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Transfer;

use Upgrader\Business\PackageManagementSystem\Transfer\Collection\ModuleTransferCollection;

class ReleaseGroupTransfer
{
    /**
     * @var \Upgrader\Business\PackageManagementSystem\Transfer\Collection\ModuleTransferCollection
     */
    protected $moduleCollection;

    /**
     * @var bool
     */
    protected $containsProjectChanges;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     * @param \Upgrader\Business\PackageManagementSystem\Transfer\Collection\ModuleTransferCollection $moduleCollection
     * @param bool $containsProjectChanges
     */
    public function __construct(string $name, ModuleTransferCollection $moduleCollection, bool $containsProjectChanges)
    {
        $this->name = $name;
        $this->moduleCollection = $moduleCollection;
        $this->containsProjectChanges = $containsProjectChanges;
    }

    /**
     * @return \Upgrader\Business\PackageManagementSystem\Transfer\Collection\ModuleTransferCollection
     */
    public function getModuleCollection(): ModuleTransferCollection
    {
        return $this->moduleCollection;
    }

    /**
     * @return bool
     */
    public function isContainsProjectChanges(): bool
    {
        return $this->containsProjectChanges;
    }

    /**
     * @return bool
     */
    public function isContainsMajorUpdates(): bool
    {
        return $this->moduleCollection->isContainsMajorUpdates();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
