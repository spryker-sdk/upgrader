<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Processor;

use PackageManager\Domain\Dto\Collection\PackageDtoCollection;
use PackageManager\Domain\Dto\PackageManagerResponseDto;
use ReleaseApp\Infrastructure\Presentation\Entity\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Presentation\Entity\Collection\ReleaseGroupDtoCollection;
use Upgrade\Domain\Adapter\PackageManagerAdapterInterface;
use Upgrade\Domain\Dto\Step\StepsExecutionDto;
use Upgrade\Domain\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface;
use Upgrade\Domain\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface;
use Upgrade\Domain\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class AggregateReleaseGroupRequireProcessor implements ReleaseGroupRequireProcessorInterface
{
    /**
     * @var \Upgrade\Domain\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface
     */
    protected ReleaseGroupSoftValidatorInterface $releaseGroupValidator;

    /**
     * @var \Upgrade\Domain\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface
     */
    protected ThresholdSoftValidatorInterface $thresholdValidator;

    /**
     * @var \Upgrade\Domain\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface
     */
    protected PackageCollectionMapperInterface $packageCollectionMapper;

    /**
     * @var \Upgrade\Domain\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManager;

    /**
     * @param \Upgrade\Domain\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager
     * @param \Upgrade\Domain\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface $thresholdSoftValidator
     * @param \Upgrade\Domain\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface $packageCollectionBuilder
     * @param \Upgrade\Domain\Adapter\PackageManagerAdapterInterface $packageManager
     */
    public function __construct(
        ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager,
        ThresholdSoftValidatorInterface $thresholdSoftValidator,
        PackageCollectionMapperInterface $packageCollectionBuilder,
        PackageManagerAdapterInterface $packageManager
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
        return ConfigurationProvider::AGGREGATE_RELEASE_GROUP_REQUIRE_PROCESSOR;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Presentation\Entity\Collection\ReleaseGroupDtoCollection $requiteRequestCollection
     * @param \Upgrade\Domain\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Domain\Dto\Step\StepsExecutionDto
     */
    public function requireCollection(ReleaseGroupDtoCollection $requiteRequestCollection, StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $aggregatedReleaseGroupCollection = new ReleaseGroupDtoCollection();
        foreach ($requiteRequestCollection->toArray() as $releaseGroup) {
            $thresholdValidationResult = $this->thresholdValidator->isWithInThreshold($aggregatedReleaseGroupCollection);
            if (!$thresholdValidationResult->isSuccess()) {
                $stepsExecutionDto->setIsSuccessful(false);
                $stepsExecutionDto->addOutputMessage($thresholdValidationResult->getOutput());

                break;
            }

            $releaseGroupValidateResult = $this->releaseGroupValidator->isValidReleaseGroup($releaseGroup);
            if (!$releaseGroupValidateResult->isSuccess()) {
                $stepsExecutionDto->setIsSuccessful(false);
                $stepsExecutionDto->addOutputMessage($releaseGroupValidateResult->getOutput());

                break;
            }

            $aggregatedReleaseGroupCollection->add($releaseGroup);
        }

        $requireResult = $this->require($aggregatedReleaseGroupCollection->getCommonModuleCollection());
        if (!$requireResult->isSuccess()) {
            $stepsExecutionDto->addOutputMessage($requireResult->getOutput());
        }

        return $stepsExecutionDto;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Presentation\Entity\Collection\ModuleDtoCollection $moduleCollection
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
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
