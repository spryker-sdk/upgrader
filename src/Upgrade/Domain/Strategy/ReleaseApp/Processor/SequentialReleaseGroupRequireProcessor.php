<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Processor;

use ReleaseAppClient\Domain\Dto\Collection\ModuleDtoCollection;
use ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection;
use ReleaseAppClient\Domain\Dto\ReleaseGroupDto;
use PackageManager\Domain\Dto\Collection\PackageDtoCollection;
use PackageManager\Domain\Dto\Collection\PackageManagerResponseDtoCollection;
use PackageManager\Domain\Dto\PackageManagerResponseDto;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use PackageManager\Application\Service\PackageManagerServiceInterface;
use Upgrade\Domain\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorInterface;
use Upgrade\Domain\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface;
use Upgrade\Domain\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface;
use Upgrade\Domain\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface;

class SequentialReleaseGroupRequireProcessor implements ReleaseGroupRequireProcessorInterface
{
    /**
     * @var \Upgrade\Domain\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface
     */
    protected $releaseGroupValidator;

    /**
     * @var ThresholdSoftValidatorInterface
     */
    protected ThresholdSoftValidatorInterface $thresholdValidator;

    /**
     * @var \Upgrade\Domain\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface
     */
    protected $packageCollectionMapper;

    /**
     * @var \PackageManager\Application\Service\PackageManagerServiceInterface
     */
    protected $packageManager;

    /**
     * @param \Upgrade\Domain\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager
     * @param ThresholdSoftValidatorInterface  $thresholdSoftValidator
     * @param \Upgrade\Domain\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface $packageCollectionBuilder
     * @param \PackageManager\Application\Service\PackageManagerServiceInterface $packageManager
     */
    public function __construct(
        ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager,
        ThresholdSoftValidatorInterface   $thresholdSoftValidator,
        PackageCollectionMapperInterface   $packageCollectionBuilder,
        PackageManagerServiceInterface $packageManager
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
     * @param \ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection $requiteRequestCollection
     *
     * @return \PackageManager\Domain\Dto\Collection\PackageManagerResponseDtoCollection
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
     * @param \ReleaseAppClient\Domain\Dto\ReleaseGroupDto $releaseGroup
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
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
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
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
