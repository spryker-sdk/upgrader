<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Processor;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Bridge\PackageManagerBridgeInterface;
use Upgrade\Application\Dto\ExecutionDto;
use Upgrade\Application\Dto\StepsExecutionDto;
use Upgrade\Application\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class SequentialReleaseGroupRequireProcessor implements ReleaseGroupRequireProcessorInterface
{
    /**
     * @var \Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface
     */
    protected $releaseGroupValidator;

    /**
     * @var \Upgrade\Application\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface
     */
    protected ThresholdSoftValidatorInterface $thresholdValidator;

    /**
     * @var \Upgrade\Application\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface
     */
    protected $packageCollectionMapper;

    /**
     * @var \Upgrade\Application\Bridge\PackageManagerBridgeInterface
     */
    protected PackageManagerBridgeInterface $packageManager;

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager
     * @param \Upgrade\Application\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface $thresholdSoftValidator
     * @param \Upgrade\Application\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface $packageCollectionBuilder
     * @param \Upgrade\Application\Bridge\PackageManagerBridgeInterface $packageManager
     */
    public function __construct(
        ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager,
        ThresholdSoftValidatorInterface $thresholdSoftValidator,
        PackageCollectionMapperInterface $packageCollectionBuilder,
        PackageManagerBridgeInterface $packageManager
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
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection $requiteRequestCollection
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function requireCollection(ReleaseGroupDtoCollection $requiteRequestCollection, StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $aggregatedReleaseGroupCollection = new ReleaseGroupDtoCollection();

        foreach ($requiteRequestCollection->toArray() as $releaseGroup) {
            $thresholdValidationResult = $this->thresholdValidator->isWithInThreshold($aggregatedReleaseGroupCollection);
            if (!$thresholdValidationResult->isSuccessful()) {
                $stepsExecutionDto->addOutputMessage($thresholdValidationResult->getOutputMessage());

                break;
            }
            $aggregatedReleaseGroupCollection->add($releaseGroup);

            $validateResult = $this->releaseGroupValidator->isValidReleaseGroup($releaseGroup);
            if (!$validateResult->isSuccessful()) {
                $stepsExecutionDto->addOutputMessage($validateResult->getOutputMessage());
                $stepsExecutionDto->setPullRequestWarning($validateResult->getOutputMessage());

                break;
            }

            $requireResult = $this->require($releaseGroup);
            if (!$requireResult->isSuccessful()) {
                $stepsExecutionDto->setIsSuccessful(false);
                $stepsExecutionDto->addOutputMessage($requireResult->getOutputMessage());

                break;
            }
        }

        return $stepsExecutionDto;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroup
     *
     * @return \Upgrade\Application\Dto\ExecutionDto
     */
    public function require(ReleaseGroupDto $releaseGroup): ExecutionDto
    {
        $moduleCollection = $releaseGroup->getModuleCollection();
        $packageCollection = $this->packageCollectionMapper->mapModuleCollectionToPackageCollection($moduleCollection);
        $filteredPackageCollection = $this->packageCollectionMapper->filterInvalidPackage($packageCollection);

        if ($filteredPackageCollection->isEmpty()) {
            $packagesNameString = implode(' ', $packageCollection->getNameList());

            return new ExecutionDto(true, $packagesNameString);
        }

        return $this->requirePackageCollection($filteredPackageCollection);
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\ExecutionDto
     */
    protected function requirePackageCollection(PackageCollection $packageCollection): ExecutionDto
    {
        $requiredPackages = $this->packageCollectionMapper->getRequiredPackages($packageCollection);
        $requiredDevPackages = $this->packageCollectionMapper->getRequiredDevPackages($packageCollection);

        if (!$requiredPackages->isEmpty()) {
            $requireResponse = $this->packageManager->require($requiredPackages);
            if (!$requireResponse->isSuccessful()) {
                return $requireResponse;
            }
        }

        if (!$requiredDevPackages->isEmpty()) {
            $requireResponse = $this->packageManager->requireDev($requiredDevPackages);
            if (!$requireResponse->isSuccessful()) {
                return $requireResponse;
            }
        }

        $packagesNameString = implode(' ', $packageCollection->getNameList());

        return new ExecutionDto(true, $packagesNameString);
    }
}