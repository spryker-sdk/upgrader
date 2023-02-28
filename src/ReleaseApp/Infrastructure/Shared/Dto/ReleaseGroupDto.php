<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Infrastructure\Shared\Dto;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;

class ReleaseGroupDto
{
    /**
     * @var \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection
     */
    protected ModuleDtoCollection $moduleCollection;

    /**
     * @var bool
     */
    protected bool $containsProjectChanges;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $link;

    /**
     * @var bool
     */
    protected bool $conflictDetected = false;

    /**
     * @param string $name
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection $moduleCollection
     * @param bool $containsProjectChanges
     * @param string $link
     * @param bool $conflictDetected
     */
    public function __construct(
        string $name,
        ModuleDtoCollection $moduleCollection,
        bool $containsProjectChanges,
        string $link,
        bool $conflictDetected = false
    ) {
        $this->name = $name;
        $this->link = $link;
        $this->moduleCollection = $moduleCollection;
        $this->containsProjectChanges = $containsProjectChanges;
        $this->conflictDetected = $conflictDetected;
    }

    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection
     */
    public function getModuleCollection(): ModuleDtoCollection
    {
        return $this->moduleCollection;
    }

    /**
     * @return bool
     */
    public function hasProjectChanges(): bool
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

    /**
     * @return bool
     */
    public function isConflictDetected(): bool
    {
        return $this->conflictDetected;
    }

    /**
     * @param bool $conflictDetected
     *
     * @return void
     */
    public function setConflictDetected(bool $conflictDetected): void
    {
        $this->conflictDetected = $conflictDetected;
    }
}
