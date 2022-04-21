<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Processor;

use Upgrade\Application\Dto\ReleaseAppClient\Collection\ModuleDtoCollection;
use Upgrade\Application\Dto\ReleaseAppClient\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Dto\ReleaseAppClient\ReleaseGroupDto;
use Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection;
use Upgrade\Application\Dto\PackageManager\Collection\PackageManagerResponseDtoCollection;
use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\PackageManager\PackageManagerInterface;
use Upgrade\Infrastructure\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorInterface;
use Upgrade\Infrastructure\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface;
use Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface;

class AggregateReleaseGroupRequireProcessor implements ReleaseGroupRequireProcessorInterface
{
    /**
     * @var \Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface
     */
    protected $releaseGroupValidateManager;

    /**
     * @var \Upgrade\Infrastructure\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface
     */
    protected $packageCollectionMapper;

    /**
     * @var \Upgrade\Infrastructure\PackageManager\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @param \Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager
     * @param \Upgrade\Infrastructure\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface $packageCollectionBuilder
     * @param \Upgrade\Infrastructure\PackageManager\PackageManagerInterface $packageManager
     */
    public function __construct(
        ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager,
        PackageCollectionMapperInterface   $packageCollectionBuilder,
        PackageManagerInterface            $packageManager
    ) {
        $this->releaseGroupValidateManager = $releaseGroupValidateManager;
        $this->packageCollectionMapper = $packageCollectionBuilder;
        $this->packageManager = $packageManager;
    }

    /**
     * @return string
     */
    public function getProcessorName(): string
    {
        return ConfigurationProvider::AGGREGATE_RELEASE_GROUP_REQUIRE_PROCESSOR;
    }

    /**
     * @param \Upgrade\Application\Dto\ReleaseAppClient\Collection\ReleaseGroupDtoCollection $releaseGroupCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageManagerResponseDtoCollection
     */
    public function requireCollection(ReleaseGroupDtoCollection $releaseGroupCollection): PackageManagerResponseDtoCollection
    {
        $responseDtoCollection = new PackageManagerResponseDtoCollection();
        $aggregateModuleCollection = new ModuleDtoCollection();
        foreach ($releaseGroupCollection as $releaseGroup) {
            var_dump('RG_NAME: ' . $releaseGroup->getName());
            $validateResult = $this->releaseGroupValidateManager->isValidReleaseGroup($releaseGroup);
            if (!$validateResult->isSuccess()) {
                var_dump($validateResult->getOutput());
                $responseDtoCollection->add($validateResult);
                break;
            }
            $aggregateModuleCollection->addCollection($releaseGroup->getModuleCollection());
        }

        $requireResult = $this->require($aggregateModuleCollection);
        $responseDtoCollection->add($requireResult);

        return $responseDtoCollection;
    }

    /**
     * @param ModuleDtoCollection $moduleCollection
     * @return PackageManagerResponseDto
     */
    public function require(ModuleDtoCollection $moduleCollection): PackageManagerResponseDto
    {
        $packageCollection = $this->packageCollectionMapper->mapModuleCollectionToPackageCollection($moduleCollection);
        $filteredPackageCollection = $this->packageCollectionMapper->filterInvalidPackage($packageCollection);

        if ($filteredPackageCollection->isEmpty()) {
            $packagesNameString = implode(' ', $packageCollection->getNameList());

            return new PackageManagerResponseDto(true, $packagesNameString);
        }

        return $this->requirePackageCollection($filteredPackageCollection);
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto
     */
    protected function requirePackageCollection(PackageDtoCollection $packageCollection): PackageManagerResponseDto
    {
        $requiredPackages = $this->packageCollectionMapper->getRequiredPackages($packageCollection);
        $requiredDevPackages = $this->packageCollectionMapper->getRequiredDevPackages($packageCollection);

        if (!$requiredPackages->isEmpty()) {
            $requireResponse = $this->packageManager->require($requiredPackages);
            if (!$requireResponse->isSuccess()) {
                return $requireResponse;
            }
        }

        if (!$requiredDevPackages->isEmpty()) {
            $requireResponse = $this->packageManager->requireDev($requiredDevPackages);
            if (!$requireResponse->isSuccess()) {
                return $requireResponse;
            }
        }

        $packagesNameString = implode(' ', $packageCollection->getNameList());

        return new PackageManagerResponseDto(true, $packagesNameString, $packageCollection->getNameList());
    }
}
