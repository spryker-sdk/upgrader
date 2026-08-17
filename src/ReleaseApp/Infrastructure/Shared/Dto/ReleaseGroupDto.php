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
    protected ModuleDtoCollection $moduleCollection;

    protected bool $containsProjectChanges;

    protected int $id;

    protected string $name;

    protected DateTimeInterface $released;

    protected string $link;

    protected int $rating;

    protected ?string $jiraIssue = null;

    protected ?string $jiraIssueLink = null;

    protected bool $hasConflict = false;

    protected bool $isSecurity = false;

    protected ?string $integrationGuide;

    protected bool $manualActionNeeded;

    protected ModuleDtoCollection $backportModuleCollection;

    protected ModuleDtoCollection $featurePackages;

    public function __construct(
        int $id,
        string $name,
        ModuleDtoCollection $moduleCollection,
        ModuleDtoCollection $backportModuleCollection,
        ModuleDtoCollection $featurePackages,
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
        $this->featurePackages = $featurePackages;
        $this->containsProjectChanges = $containsProjectChanges;
        $this->hasConflict = $hasConflict;
        $this->rating = $rating;
        $this->isSecurity = $isSecurity;
        $this->integrationGuide = $integrationGuide;
        $this->manualActionNeeded = $manualActionNeeded;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getModuleCollection(): ModuleDtoCollection
    {
        return $this->moduleCollection;
    }

    public function setModuleCollection(ModuleDtoCollection $moduleCollection): void
    {
        $this->moduleCollection = $moduleCollection;
    }

    public function getFeaturePackages(): ModuleDtoCollection
    {
        return $this->featurePackages;
    }

    public function setFeaturePackages(ModuleDtoCollection $featurePackages): void
    {
        $this->featurePackages = $featurePackages;
    }

    public function getBackportModuleCollection(): ModuleDtoCollection
    {
        return $this->backportModuleCollection;
    }

    public function hasProjectChanges(): bool
    {
        return $this->containsProjectChanges;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function hasConflict(): bool
    {
        return $this->hasConflict;
    }

    public function setHasConflict(bool $hasConflict): void
    {
        $this->hasConflict = $hasConflict;
    }

    public function getJiraIssue(): ?string
    {
        return $this->jiraIssue;
    }

    public function setJiraIssue(?string $jiraIssue): void
    {
        $this->jiraIssue = $jiraIssue;
    }

    public function getJiraIssueLink(): ?string
    {
        return $this->jiraIssueLink;
    }

    public function setJiraIssueLink(?string $jiraIssueLink): void
    {
        $this->jiraIssueLink = $jiraIssueLink;
    }

    public function setIsSecurity(bool $isSecurity): void
    {
        $this->isSecurity = $isSecurity;
    }

    public function isSecurity(): bool
    {
        return $this->isSecurity;
    }

    public function setIntegrationGuide(?string $integrationGuide): void
    {
        $this->integrationGuide = $integrationGuide;
    }

    public function getIntegrationGuide(): ?string
    {
        return $this->integrationGuide;
    }

    public function setManualActionNeeded(bool $manualActionNeeded): void
    {
        $this->manualActionNeeded = $manualActionNeeded;
    }

    public function getManualActionNeeded(): bool
    {
        return $this->manualActionNeeded;
    }

    public function getReleased(): DateTimeInterface
    {
        return $this->released;
    }
}
