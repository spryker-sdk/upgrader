<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Infrastructure\Shared\Mapper;

use ReleaseApp\Application\Configuration\ConfigurationProviderInterface;
use ReleaseApp\Application\Configuration\ReleaseAppConstant;
use ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection;
use ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection;
use ReleaseApp\Domain\Entities\UpgradeInstructionMeta;
use ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;

class ReleaseGroupDtoCollectionMapper
{
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @param \ReleaseApp\Application\Configuration\ConfigurationProviderInterface $configurationProvider
     */
    public function __construct(ConfigurationProviderInterface $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @param \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection $releaseGroupCollection
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection
     */
    public function mapReleaseGroupTransferCollection(
        UpgradeInstructionsReleaseGroupCollection $releaseGroupCollection
    ): ReleaseGroupDtoCollection {
        $dataProviderReleaseGroupCollection = new ReleaseGroupDtoCollection();

        foreach ($releaseGroupCollection->toArray() as $releaseGroup) {
            $dataProviderReleaseGroupCollection->add($this->mapReleaseGroupDto($releaseGroup));
        }

        return $dataProviderReleaseGroupCollection;
    }

    /**
     * @param \ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup $releaseGroup
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection
     */
    public function mapReleaseGroupDtoIntoCollection(UpgradeInstructionsReleaseGroup $releaseGroup): ReleaseGroupDtoCollection
    {
        $dataProviderReleaseGroupCollection = new ReleaseGroupDtoCollection();
        $dataProviderReleaseGroupCollection->add($this->mapReleaseGroupDto($releaseGroup));

        return $dataProviderReleaseGroupCollection;
    }

    /**
     * @param \ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup $releaseGroup
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto
     */
    protected function mapReleaseGroupDto(UpgradeInstructionsReleaseGroup $releaseGroup): ReleaseGroupDto
    {
        $dataProviderReleaseGroup = new ReleaseGroupDto(
            $releaseGroup->getId(),
            $releaseGroup->getName(),
            $this->buildModuleTransferCollection($releaseGroup),
            $this->buildBackportModuleTransferCollection($releaseGroup),
            new ModuleDtoCollection(),
            $releaseGroup->getReleased(),
            $releaseGroup->hasProjectChanges(),
            $this->getReleaseGroupLink($releaseGroup->getId()),
            $releaseGroup->getRating(),
        );
        $dataProviderReleaseGroup->setHasConflict(
            $releaseGroup->getMeta() && $releaseGroup->getMeta()->getConflict()->count(),
        );
        $dataProviderReleaseGroup->setJiraIssue($releaseGroup->getJiraIssue());
        $dataProviderReleaseGroup->setJiraIssueLink($releaseGroup->getJiraIssueLink());
        $dataProviderReleaseGroup->setIsSecurity($releaseGroup->isSecurity());
        $dataProviderReleaseGroup->setIntegrationGuide($releaseGroup->getIntegrationGuide());
        $dataProviderReleaseGroup->setManualActionNeeded($releaseGroup->getManualActionNeeded());

        return $dataProviderReleaseGroup;
    }

    /**
     * @param \ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup $releaseGroup
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection
     */
    protected function buildModuleTransferCollection(UpgradeInstructionsReleaseGroup $releaseGroup): ModuleDtoCollection
    {
        $releaseGroupModuleCollection = $releaseGroup->getModuleCollection();
        if ($releaseGroup->getMeta()) {
            $releaseGroupModuleCollection = $this->applyMeta($releaseGroupModuleCollection, $releaseGroup->getMeta());
        }

        $dataProviderModuleCollection = new ModuleDtoCollection();
        foreach ($releaseGroupModuleCollection->toArray() as $module) {
            $dataProviderModule = new ModuleDto($module->getName(), $module->getVersion(), $module->getType(), $module->getFeaturePackages());
            $dataProviderModuleCollection->add($dataProviderModule);
        }

        return $dataProviderModuleCollection;
    }

    /**
     * @param \ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup $releaseGroup
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection
     */
    protected function buildBackportModuleTransferCollection(UpgradeInstructionsReleaseGroup $releaseGroup): ModuleDtoCollection
    {
        $releaseGroupModuleCollection = $releaseGroup->getBackportModuleCollection();

        $dataProviderModuleCollection = new ModuleDtoCollection();
        foreach ($releaseGroupModuleCollection->toArray() as $module) {
            $dataProviderModule = new ModuleDto($module->getName(), $module->getVersion(), $module->getType());
            $dataProviderModuleCollection->add($dataProviderModule);
        }

        return $dataProviderModuleCollection;
    }

    /**
     * @param int $id
     *
     * @return string
     */
    protected function getReleaseGroupLink(int $id): string
    {
        return sprintf(ReleaseAppConstant::RELEASE_GROUP_LINK_PATTERN, $this->configurationProvider->getReleaseAppUrl(), $id);
    }

    /**
     * @param \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection $moduleCollection
     * @param \ReleaseApp\Domain\Entities\UpgradeInstructionMeta $meta
     *
     * @return \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection
     */
    protected function applyMeta(
        UpgradeInstructionModuleCollection $moduleCollection,
        UpgradeInstructionMeta $meta
    ): UpgradeInstructionModuleCollection {
        foreach ($meta->getInclude()->toArray() as $moduleInclude) {
            $module = $moduleCollection->getByName($moduleInclude->getName());
            if ($module) {
                $module->setVersion($moduleInclude->getVersion());

                continue;
            }

            $moduleInclude->setType(ReleaseAppConstant::MODULE_TYPE_PATCH);
            $moduleCollection->add($moduleInclude);
        }
        foreach ($meta->getExclude()->toArray() as $moduleExclude) {
            $moduleCollection->deleteByName($moduleExclude->getName());
        }

        return $moduleCollection;
    }
}
