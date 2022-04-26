<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Dto;

use ReleaseAppClient\Domain\Dto\Collection\ModuleDtoCollection;

class ReleaseGroupDto
{
    /**
     * @var \ReleaseAppClient\Domain\Dto\Collection\ModuleDtoCollection
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
     * @var string
     */
    protected $link;

    /**
     * @param string $name
     * @param \ReleaseAppClient\Domain\Dto\Collection\ModuleDtoCollection $moduleCollection
     * @param bool $containsProjectChanges
     * @param string $link
     */
    public function __construct(string $name, ModuleDtoCollection $moduleCollection, bool $containsProjectChanges, string $link)
    {
        $this->name = $name;
        $this->link = $link;
        $this->moduleCollection = $moduleCollection;
        $this->containsProjectChanges = $containsProjectChanges;
    }

    /**
     * @return \ReleaseAppClient\Domain\Dto\Collection\ModuleDtoCollection
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

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }
}
