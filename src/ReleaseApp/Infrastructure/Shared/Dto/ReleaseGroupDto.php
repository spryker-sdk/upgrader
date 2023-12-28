<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Infrastructure\Shared\Dto;

use DateTimeInterface;
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
     * @var int
     */
    protected int $id;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var \DateTimeInterface
     */
    protected DateTimeInterface $released;

    /**
     * @var string
     */
    protected string $link;

    /**
     * @var int
     */
    protected int $rating;

    /**
     * @var string|null
     */
    protected ?string $jiraIssue = null;

    /**
     * @var string|null
     */
    protected ?string $jiraIssueLink = null;

    /**
     * @var bool
     */
    protected bool $hasConflict = false;

    /**
     * @var bool
     */
    protected bool $isSecurity = false;

    /**
     * @var string|null
     */
    protected ?string $integrationGuide;

    /**
     * @var bool
     */
    protected bool $manualActionNeeded;

    /**
     * @var \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection
     */
    protected ModuleDtoCollection $backportModuleCollection;

    /**
     * @param int $id
     * @param string $name
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection $moduleCollection
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection $backportModuleCollection
     * @param \DateTimeInterface $released
     * @param bool $containsProjectChanges
     * @param string $link
     * @param int $rating
     * @param bool $hasConflict
     * @param bool $isSecurity
     * @param string|null $integrationGuide
     * @param bool $manualActionNeeded
     */
    public function __construct(
        int $id,
        string $name,
        ModuleDtoCollection $moduleCollection,
        ModuleDtoCollection $backportModuleCollection,
        DateTimeInterface $released,
        bool $containsProjectChanges,
        string $link,
        int $rating,
        bool $hasConflict = false,
        bool $isSecurity = false,
        ?string $integrationGuide = null,
        bool $manualActionNeeded = false
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->released = $released;
        $this->link = $link;
        $this->moduleCollection = $moduleCollection;
        $this->backportModuleCollection = $backportModuleCollection;
        $this->containsProjectChanges = $containsProjectChanges;
        $this->hasConflict = $hasConflict;
        $this->rating = $rating;
        $this->isSecurity = $isSecurity;
        $this->integrationGuide = $integrationGuide;
        $this->manualActionNeeded = $manualActionNeeded;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection
     */
    public function getModuleCollection(): ModuleDtoCollection
    {
        return $this->moduleCollection;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection $moduleCollection
     *
     * @return void
     */
    public function setModuleCollection(ModuleDtoCollection $moduleCollection): void
    {
        $this->moduleCollection = $moduleCollection;
    }

    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection
     */
    public function getBackportModuleCollection(): ModuleDtoCollection
    {
        return $this->backportModuleCollection;
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
     * @return int
     */
    public function getRating(): int
    {
        return $this->rating;
    }

    /**
     * @return bool
     */
    public function hasConflict(): bool
    {
        return $this->hasConflict;
    }

    /**
     * @param bool $hasConflict
     *
     * @return void
     */
    public function setHasConflict(bool $hasConflict): void
    {
        $this->hasConflict = $hasConflict;
    }

    /**
     * @return string|null
     */
    public function getJiraIssue(): ?string
    {
        return $this->jiraIssue;
    }

    /**
     * @param string|null $jiraIssue
     *
     * @return void
     */
    public function setJiraIssue(?string $jiraIssue): void
    {
        $this->jiraIssue = $jiraIssue;
    }

    /**
     * @return string|null
     */
    public function getJiraIssueLink(): ?string
    {
        return $this->jiraIssueLink;
    }

    /**
     * @param string|null $jiraIssueLink
     *
     * @return void
     */
    public function setJiraIssueLink(?string $jiraIssueLink): void
    {
        $this->jiraIssueLink = $jiraIssueLink;
    }

    /**
     * @param bool $isSecurity
     *
     * @return void
     */
    public function setIsSecurity(bool $isSecurity): void
    {
        $this->isSecurity = $isSecurity;
    }

    /**
     * @return bool
     */
    public function isSecurity(): bool
    {
        return $this->isSecurity;
    }

    /**
     * @param string|null $integrationGuide
     *
     * @return void
     */
    public function setIntegrationGuide(?string $integrationGuide): void
    {
        $this->integrationGuide = $integrationGuide;
    }

    /**
     * @return string|null
     */
    public function getIntegrationGuide(): ?string
    {
        return $this->integrationGuide;
    }

    /**
     * @param bool $manualActionNeeded
     *
     * @return void
     */
    public function setManualActionNeeded(bool $manualActionNeeded): void
    {
        $this->manualActionNeeded = $manualActionNeeded;
    }

    /**
     * @return bool
     */
    public function getManualActionNeeded(): bool
    {
        return $this->manualActionNeeded;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getReleased(): DateTimeInterface
    {
        return $this->released;
    }
}
