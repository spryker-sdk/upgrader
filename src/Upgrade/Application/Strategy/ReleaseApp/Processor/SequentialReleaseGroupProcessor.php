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
     * @var \Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcher
     */
    protected ModuleFetcher $moduleFetcher;

    /**
     * @var \Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\ReleaseGroupFilterInterface
     */
    protected ReleaseGroupFilterInterface $releaseGroupPackageFilter;

    /**
     * @var \Upgrade\Application\Executor\StepExecutorInterface
     */
    protected StepExecutorInterface $preRequireHookExecutor;

    /**
     * @var \Upgrade\Application\Executor\StepExecutorInterface
     */
    protected StepExecutorInterface $postRequireHookExecutor;

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager
     * @param \Upgrade\Application\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface $thresholdSoftValidator
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcher $moduleFetcher
     * @param \Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\ReleaseGroupFilterInterface $releaseGroupFilter
     * @param \Upgrade\Application\Executor\StepExecutorInterface $preRequireHookExecutor
     * @param \Upgrade\Application\Executor\StepExecutorInterface $postRequireHookExecutor
     */
    public function __construct(
        ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager,
        ThresholdSoftValidatorInterface $thresholdSoftValidator,
        ModuleFetcher $moduleFetcher,
        ReleaseGroupFilterInterface $releaseGroupFilter,
        StepExecutorInterface $preRequireHookExecutor,
        StepExecutorInterface $postRequireHookExecutor
    ) {
        $this->releaseGroupValidator = $releaseGroupValidateManager;
        $this->thresholdValidator = $thresholdSoftValidator;
        $this->moduleFetcher = $moduleFetcher;
        $this->releaseGroupPackageFilter = $releaseGroupFilter;
        $this->preRequireHookExecutor = $preRequireHookExecutor;
        $this->postRequireHookExecutor = $postRequireHookExecutor;
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
        if ($requireRequestCollection->isEmpty()) {
            return $stepsExecutionDto
                ->setIsStopPropagation(true)
                ->addOutputMessage('The branch is up to date. No further action is required.');
        }

        $aggregatedReleaseGroupCollection = new ReleaseGroupDtoCollection();
        foreach ($requireRequestCollection->toArray() as $releaseGroup) {
            $releaseGroup = $this->releaseGroupPackageFilter->filter($releaseGroup);
            $thresholdValidationResult = $this->thresholdValidator->validate($aggregatedReleaseGroupCollection);
            if (!$thresholdValidationResult->isSuccessful()) {
                $stepsExecutionDto->addOutputMessage($thresholdValidationResult->getOutputMessage());

                break;
            }

            $validateResult = $this->releaseGroupValidator->isValidReleaseGroup($releaseGroup);
            if (!$validateResult->isSuccessful()) {
                $stepsExecutionDto->addOutputMessage($validateResult->getOutputMessage());
                $stepsExecutionDto->setBlockerInfo((string)$validateResult->getOutputMessage());

                break;
            }

            $stepsExecutionDto = $this->preRequireHookExecutor->execute($stepsExecutionDto);
            if (!$stepsExecutionDto->isSuccessful() || $stepsExecutionDto->getIsStopPropagation()) {
                break;
            }

            $response = $this->moduleFetcher->require($releaseGroup->getModuleCollection());
            if ($response->getOutputMessage() !== null) {
                $stepsExecutionDto->addOutputMessage($response->getOutputMessage());
            }
            if (!$response->isSuccessful()) {
                $stepsExecutionDto->setIsSuccessful(false);

                break;
            }
            $stepsExecutionDto->setLastAppliedReleaseGroup($releaseGroup);
            $aggregatedReleaseGroupCollection->add($releaseGroup);

            $stepsExecutionDto = $this->postRequireHookExecutor->execute($stepsExecutionDto);
            if (!$stepsExecutionDto->isSuccessful() || $stepsExecutionDto->getIsStopPropagation()) {
                break;
            }
        }

        if ($aggregatedReleaseGroupCollection->count()) {
            $stepsExecutionDto->addOutputMessage(
                sprintf('Amount of applied release groups: %s', $aggregatedReleaseGroupCollection->count()),
            );
        }
        if (!$stepsExecutionDto->getComposerLockDiff() || $stepsExecutionDto->getComposerLockDiff()->isEmpty()) {
            return $stepsExecutionDto
                ->setIsStopPropagation(true)
                ->addOutputMessage('The branch is up to date. No further action is required.');
        }

        return $stepsExecutionDto;
    }
}
