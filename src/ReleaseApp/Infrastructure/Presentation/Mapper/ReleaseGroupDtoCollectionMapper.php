<?php

namespace ReleaseApp\Infrastructure\Presentation\Mapper;

use ReleaseApp\Infrastructure\Presentation\Entity\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Presentation\Entity\Collection\ReleaseGroupDtoCollection;
use ReleaseApp\Infrastructure\Presentation\Entity\ModuleDto;
use ReleaseApp\Infrastructure\Presentation\Entity\ReleaseGroupDto;
use ReleaseApp\Domain\Entities\UpgradeInstructions\Response\Collection\UpgradeInstructionsReleaseGroupCollection;
use ReleaseApp\Domain\Entities\UpgradeInstructions\Response\UpgradeInstructionsReleaseGroup;
use ReleaseApp\Application\Configuration\ReleaseAppConst;

class ReleaseGroupDtoCollectionMapper
{
    /**
     * @param \ReleaseApp\Domain\Entities\UpgradeInstructions\Response\Collection\UpgradeInstructionsReleaseGroupCollection $releaseGroupCollection
     *
     * @return \ReleaseApp\Infrastructure\Presentation\Entity\Collection\ReleaseGroupDtoCollection
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
     * @param \ReleaseApp\Domain\Entities\UpgradeInstructions\Response\UpgradeInstructionsReleaseGroup $releaseGroup
     *
     * @return \ReleaseApp\Infrastructure\Presentation\Entity\Collection\ModuleDtoCollection
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
        return sprintf(ReleaseAppConst::RELEASE_GROUP_LINK_TEMPLATE, $this->configurationProvider->getReleaseAppUrl(), $id);
    }
}
