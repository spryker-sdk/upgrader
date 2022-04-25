<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Processor;

use ReleaseAppClient\Domain\Dto\Collection\ModuleDtoCollection;
use ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection;
use PackageManager\Domain\Dto\Collection\PackageDtoCollection;
use PackageManager\Domain\Dto\Collection\PackageManagerResponseDtoCollection;
use PackageManager\Domain\Dto\PackageManagerResponseDto;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use PackageManager\Application\Service\PackageManagerInterface;
use Upgrade\Infrastructure\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface;
use Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface;
use Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface;

class AggregateReleaseGroupRequireProcessor implements ReleaseGroupRequireProcessorInterface
{
    /**
     * @var \Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface
     */
    protected ReleaseGroupSoftValidatorInterface $releaseGroupValidator;

    /**
     * @var ThresholdSoftValidatorInterface
     */
    protected ThresholdSoftValidatorInterface $thresholdValidator;

    /**
     * @var \Upgrade\Infrastructure\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface
     */
    protected PackageCollectionMapperInterface $packageCollectionMapper;

    /**
     * @var \PackageManager\Application\Service\PackageManagerInterface
     */
    protected PackageManagerInterface $packageManager;

    /**
     * @param \Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager
     * @param ThresholdSoftValidatorInterface $thresholdSoftValidator
     * @param \Upgrade\Infrastructure\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface $packageCollectionBuilder
     * @param \PackageManager\Application\Service\PackageManagerInterface $packageManager
     */
    public function __construct(
        ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager,
        ThresholdSoftValidatorInterface $thresholdSoftValidator,
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
        return ConfigurationProvider::AGGREGATE_RELEASE_GROUP_REQUIRE_PROCESSOR;
    }

    /**
     * @param \ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection $requiteRequestCollection
     *
     * @return \PackageManager\Domain\Dto\Collection\PackageManagerResponseDtoCollection
     */
    public function requireCollection(ReleaseGroupDtoCollection $requiteRequestCollection): PackageManagerResponseDtoCollection
    {
        $responseDtoCollection = new PackageManagerResponseDtoCollection();
        $aggregatedReleaseGroupCollection = new ReleaseGroupDtoCollection();
        foreach ($requiteRequestCollection as $releaseGroup) {
            var_dump('RG_NAME: ' . $releaseGroup->getName());
            $thresholdValidationResult = $this->thresholdValidator->isWithInThreshold($aggregatedReleaseGroupCollection);
            if (!$thresholdValidationResult->isSuccess()) {
                var_dump($thresholdValidationResult->getOutput());
                break;
            }

            $releaseGroupValidateResult = $this->releaseGroupValidator->isValidReleaseGroup($releaseGroup, );
            if (!$releaseGroupValidateResult->isSuccess()) {
                var_dump($releaseGroupValidateResult->getOutput());
                $responseDtoCollection->add($releaseGroupValidateResult);
                break;
            }

            $aggregatedReleaseGroupCollection->add($releaseGroup);
        }

        $requireResult = $this->require($aggregatedReleaseGroupCollection->getCommonModuleCollection());
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
