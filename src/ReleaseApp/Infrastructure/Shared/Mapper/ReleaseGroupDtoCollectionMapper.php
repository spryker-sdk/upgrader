<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Shared\Mapper;

use ReleaseApp\Application\Configuration\ConfigurationProviderInterface;
use ReleaseApp\Application\Configuration\ReleaseAppConst;
use ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection;
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
    public function buildReleaseGroupTransferCollection(
        UpgradeInstructionsReleaseGroupCollection $releaseGroupCollection
    ): ReleaseGroupDtoCollection {
        $dataProviderReleaseGroupCollection = new ReleaseGroupDtoCollection();

        foreach ($releaseGroupCollection->toArray() as $releaseGroup) {
            $dataProviderReleaseGroup = new ReleaseGroupDto(
                $releaseGroup->getName(),
                $this->buildModuleTransferCollection($releaseGroup),
                $releaseGroup->isContainsProjectChanges(),
                $this->getReleaseGroupLink($releaseGroup->getId()),
            );
            $dataProviderReleaseGroupCollection->add($dataProviderReleaseGroup);
        }

        return $dataProviderReleaseGroupCollection;
    }

    /**
     * @param \ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup $releaseGroup
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection
     */
    protected function buildModuleTransferCollection(UpgradeInstructionsReleaseGroup $releaseGroup): ModuleDtoCollection
    {
        $dataProviderModuleCollection = new ModuleDtoCollection();
        foreach ($releaseGroup->getModuleCollection()->toArray() as $module) {
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
        return sprintf(ReleaseAppConst::RELEASE_GROUP_LINK_PATTERN, $this->configurationProvider->getReleaseAppUrl(), $id);
    }
}
