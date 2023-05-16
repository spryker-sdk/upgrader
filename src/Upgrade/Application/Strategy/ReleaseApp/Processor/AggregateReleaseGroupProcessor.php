<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Executor\StepExecutorInterface;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\ReleaseGroupFilterInterface;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class AggregateReleaseGroupProcessor implements ReleaseGroupProcessorInterface
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
     * @var \Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcher
     */
    protected ModuleFetcher $modulePackageFetcher;

    /**
     * @var \Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\ReleaseGroupFilterInterface
     */
    protected ReleaseGroupFilterInterface $releaseGroupFilter;

    /**
     * @var \Upgrade\Application\Executor\StepExecutorInterface
     */
    protected StepExecutorInterface $preRequireHookExecutor;

    /**
     * @var \Upgrade\Application\Executor\StepExecutorInterface
     */
    protected StepExecutorInterface $postRequireHookExecutor;

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface $releaseGroupValidator
     * @param \Upgrade\Application\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface $thresholdValidator
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcher $modulePackageFetcher
     * @param \Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\ReleaseGroupFilterInterface $releaseGroupFilter
     * @param \Upgrade\Application\Executor\StepExecutorInterface $preRequireHookExecutor
     * @param \Upgrade\Application\Executor\StepExecutorInterface $postRequireHookExecutor
     */
    public function __construct(
        ReleaseGroupSoftValidatorInterface $releaseGroupValidator,
        ThresholdSoftValidatorInterface $thresholdValidator,
        ModuleFetcher $modulePackageFetcher,
        ReleaseGroupFilterInterface $releaseGroupFilter,
        StepExecutorInterface $preRequireHookExecutor,
        StepExecutorInterface $postRequireHookExecutor
    ) {
        $this->releaseGroupValidator = $releaseGroupValidator;
        $this->thresholdValidator = $thresholdValidator;
        $this->modulePackageFetcher = $modulePackageFetcher;
        $this->releaseGroupFilter = $releaseGroupFilter;
        $this->preRequireHookExecutor = $preRequireHookExecutor;
        $this->postRequireHookExecutor = $postRequireHookExecutor;
    }

    /**
     * @return string
     */
    public function getProcessorName(): string
    {
        return ConfigurationProvider::AGGREGATE_RELEASE_GROUP_PROCESSOR;
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
        foreach ($requireRequestCollection->toArray() as $releaseGroup) {
            $releaseGroup = $this->releaseGroupFilter->filter($releaseGroup);
            $thresholdValidationResult = $this->thresholdValidator->validate($aggregatedReleaseGroupCollection);
            if (!$thresholdValidationResult->isSuccessful()) {
                $stepsExecutionDto->addOutputMessage($thresholdValidationResult->getOutputMessage());

                break;
            }

            $releaseGroupValidateResult = $this->releaseGroupValidator->isValidReleaseGroup($releaseGroup);
            if (!$releaseGroupValidateResult->isSuccessful()) {
                $stepsExecutionDto->addOutputMessage($releaseGroupValidateResult->getOutputMessage());
                $stepsExecutionDto->setBlockerInfo((string)$releaseGroupValidateResult->getOutputMessage());

                break;
            }

            $aggregatedReleaseGroupCollection->add($releaseGroup);
        }

        $stepsExecutionDto = $this->preRequireHookExecutor->execute($stepsExecutionDto);
        if (!$stepsExecutionDto->isSuccessful() || $stepsExecutionDto->getIsStopPropagation()) {
            return $stepsExecutionDto;
        }

        $response = $this->modulePackageFetcher->require($aggregatedReleaseGroupCollection->getCommonModuleCollection());
        $stepsExecutionDto->setIsSuccessful($response->isSuccessful());
        if ($response->getOutputMessage() !== null) {
            $stepsExecutionDto->addOutputMessage($response->getOutputMessage());
        }
        if ($response->isSuccessful() && $aggregatedReleaseGroupCollection->count()) {
            $stepsExecutionDto->addOutputMessage(
                sprintf('Amount of applied release groups: %s', $aggregatedReleaseGroupCollection->count()),
            );
        }

        return $this->postRequireHookExecutor->execute($stepsExecutionDto);
    }
}
