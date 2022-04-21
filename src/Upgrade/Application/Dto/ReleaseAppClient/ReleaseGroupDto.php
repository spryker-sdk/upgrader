<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Dto\ReleaseAppClient;

use Upgrade\Application\Dto\ReleaseAppClient\Collection\ModuleDtoCollection;

class ReleaseGroupDto
{
    /**
     * @var \Upgrade\Application\Dto\ReleaseAppClient\Collection\ModuleDtoCollection
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
     * @param \Upgrade\Application\Dto\ReleaseAppClient\Collection\ModuleDtoCollection $moduleCollection
     * @param bool $containsProjectChanges
     */
    public function __construct(string $name, ModuleDtoCollection $moduleCollection, bool $containsProjectChanges)
    {
        $this->name = $name;
        $this->moduleCollection = $moduleCollection;
        $this->containsProjectChanges = $containsProjectChanges;
    }

    /**
     * @return \Upgrade\Application\Dto\ReleaseAppClient\Collection\ModuleDtoCollection
     */
    public function getModuleCollection(): ModuleDtoCollection
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
