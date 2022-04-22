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
use Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface;

class SequentialReleaseGroupRequireProcessor implements ReleaseGroupRequireProcessorInterface
{
    /**
     * @var \Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface
     */
    protected $releaseGroupValidator;

    /**
     * @var ThresholdSoftValidatorInterface
     */
    protected ThresholdSoftValidatorInterface $thresholdValidator;

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
     * @param ThresholdSoftValidatorInterface  $thresholdSoftValidator
     * @param \Upgrade\Infrastructure\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface $packageCollectionBuilder
     * @param \Upgrade\Infrastructure\PackageManager\PackageManagerInterface $packageManager
     */
    public function __construct(
        ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager,
        ThresholdSoftValidatorInterface   $thresholdSoftValidator,
        PackageCollectionMapperInterface   $packageCollectionBuilder,
        PackageManagerInterface            $packageManager
    ) {
        $this->releaseGroupValidator = $releaseGroupValidateManager;
        $this->thresholdValidator = $thresholdSoftValidator;
        $this->packageCollectionMapper = $packageCollectionBuilder;
        $this->packageManager = $packageManager;
    }

    /**
     * @return string
     */
    public function getProcessorName(): string
    {
        return ConfigurationProvider::SEQUENTIAL_RELEASE_GROUP_REQUIRE_PROCESSOR;
    }

    /**
     * @param \Upgrade\Application\Dto\ReleaseAppClient\Collection\ReleaseGroupDtoCollection $requiteRequestCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageManagerResponseDtoCollection
     */
    public function requireCollection(ReleaseGroupDtoCollection $requiteRequestCollection): PackageManagerResponseDtoCollection
    {
        $collection = new PackageManagerResponseDtoCollection();
        $aggregatedReleaseGroupCollection = new ReleaseGroupDtoCollection();

        foreach ($requiteRequestCollection as $releaseGroup) {
            $thresholdValidationResult = $this->thresholdValidator->isWithInThreshold($aggregatedReleaseGroupCollection);
            if (!$thresholdValidationResult->isSuccess()) {
                var_dump($thresholdValidationResult->getOutput());
                break;
            }
            $aggregatedReleaseGroupCollection->add($releaseGroup);

            $requireResult = $this->require($releaseGroup);
            $collection->add($requireResult);
            if (!$requireResult->isSuccess()) {
                break;
            }
        }

        return $collection;
    }

    /**
     * @param \Upgrade\Application\Dto\ReleaseAppClient\ReleaseGroupDto $releaseGroup
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto
     */
    public function require(ReleaseGroupDto $releaseGroup): PackageManagerResponseDto
    {
        $validateResult = $this->releaseGroupValidator->isValidReleaseGroup($releaseGroup);
        if (!$validateResult->isSuccess()) {
            var_dump($validateResult->getOutput());
            return $validateResult;
        }

        $moduleCollection = $releaseGroup->getModuleCollection();
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
