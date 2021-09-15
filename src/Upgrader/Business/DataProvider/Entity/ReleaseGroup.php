<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Entity;

use Upgrader\Business\DataProvider\Entity\Collection\ModuleCollection;

class ReleaseGroup
{
    /**
     * @var \Upgrader\Business\DataProvider\Entity\Collection\ModuleCollection
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
     * @param \Upgrader\Business\DataProvider\Entity\Collection\ModuleCollection $moduleCollection
     * @param bool $containsProjectChanges
     */
    public function __construct(string $name, ModuleCollection $moduleCollection, bool $containsProjectChanges)
    {
        $this->name = $name;
        $this->moduleCollection = $moduleCollection;
        $this->containsProjectChanges = $containsProjectChanges;
    }

    /**
     * @return \Upgrader\Business\DataProvider\Entity\Collection\ModuleCollection
     */
    public function getModuleCollection(): ModuleCollection
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
