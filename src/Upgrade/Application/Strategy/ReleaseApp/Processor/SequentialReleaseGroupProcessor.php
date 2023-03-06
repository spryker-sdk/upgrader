<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Dto\ResponseDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class SequentialReleaseGroupProcessor implements ReleaseGroupProcessorInterface
{
    /**
     * @var \Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface
     */
    protected ReleaseGroupSoftValidatorInterface $releaseGroupValidator;

    /**
     * @var \Upgrade\Application\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface
     */
    protected ThresholdSoftValidatorInterface $thresholdValidator;

    /**
     * @var \Upgrade\Application\Strategy\ReleaseApp\Processor\ModulePackageFetcher
     */
    protected ModulePackageFetcher $modulePackageFetcher;

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager
     * @param \Upgrade\Application\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface $thresholdSoftValidator
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\ModulePackageFetcher $modulePackageFetcher
     */
    public function __construct(
        ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager,
        ThresholdSoftValidatorInterface $thresholdSoftValidator,
        ModulePackageFetcher $modulePackageFetcher
    ) {
        $this->releaseGroupValidator = $releaseGroupValidateManager;
        $this->thresholdValidator = $thresholdSoftValidator;
        $this->modulePackageFetcher = $modulePackageFetcher;
    }

    /**
     * @return string
     */
    public function getProcessorName(): string
    {
        return ConfigurationProvider::SEQUENTIAL_RELEASE_GROUP_PROCESSOR;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection $requireRequestCollection
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function process(ReleaseGroupDtoCollection $requireRequestCollection, StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $aggregatedReleaseGroupCollection = new ReleaseGroupDtoCollection();

        $response = new ResponseDto(true);

        foreach ($requireRequestCollection->toArray() as $releaseGroup) {
            $thresholdValidationResult = $this->thresholdValidator->validate($aggregatedReleaseGroupCollection);
            if (!$thresholdValidationResult->isSuccessful()) {
                $stepsExecutionDto->addOutputMessage($thresholdValidationResult->getOutputMessage());

                break;
            }
            $aggregatedReleaseGroupCollection->add($releaseGroup);

            $validateResult = $this->releaseGroupValidator->isValidReleaseGroup($releaseGroup);
            if (!$validateResult->isSuccessful()) {
                $stepsExecutionDto->addOutputMessage($validateResult->getOutputMessage());
                $stepsExecutionDto->setBlockerInfo((string)$validateResult->getOutputMessage());

                break;
            }

            $response = $this->modulePackageFetcher->require($releaseGroup->getModuleCollection());

            if ($response->getOutputMessage() !== null) {
                $stepsExecutionDto->addOutputMessage($response->getOutputMessage());
            }

            if (!$response->isSuccessful()) {
                break;
            }
        }

        if (!$response->isSuccessful()) {
            $stepsExecutionDto->setIsSuccessful(false);
        }

        if ($response->isSuccessful() && $aggregatedReleaseGroupCollection->count()) {
            $stepsExecutionDto->addOutputMessage(
                sprintf('Amount of applied release groups: %s', $aggregatedReleaseGroupCollection->count()),
            );
        }

        return $stepsExecutionDto;
    }
}
