<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorPostRequireEvent;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\ReleaseGroupFilterInterface;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface;
use Upgrade\Application\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface;
use Upgrade\Domain\ValueObject\Error;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class AggregateReleaseGroupProcessor extends BaseReleaseGroupProcessor
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
     * @param \Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface $releaseGroupValidator
     * @param \Upgrade\Application\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface $thresholdValidator
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcher $modulePackageFetcher
     * @param \Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\ReleaseGroupFilterInterface $releaseGroupFilter
     * @param \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ReleaseGroupSoftValidatorInterface $releaseGroupValidator,
        ThresholdSoftValidatorInterface $thresholdValidator,
        ModuleFetcher $modulePackageFetcher,
        ReleaseGroupFilterInterface $releaseGroupFilter,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($eventDispatcher);

        $this->releaseGroupValidator = $releaseGroupValidator;
        $this->thresholdValidator = $thresholdValidator;
        $this->modulePackageFetcher = $modulePackageFetcher;
        $this->releaseGroupFilter = $releaseGroupFilter;
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
        if (!$this->dispatchEvent(new ReleaseGroupProcessorEvent($stepsExecutionDto), ReleaseGroupProcessorEvent::PRE_PROCESSOR)) {
            return $stepsExecutionDto;
        }

        $aggregatedReleaseGroupCollection = new ReleaseGroupDtoCollection();
        foreach ($requireRequestCollection->toArray() as $releaseGroup) {
            $releaseGroup = $this->releaseGroupFilter->filter($releaseGroup);

            if ($releaseGroup->getModuleCollection()->isEmpty()) {
                continue;
            }

            $thresholdValidationResult = $this->thresholdValidator->validate($aggregatedReleaseGroupCollection);
            if (!$thresholdValidationResult->isSuccessful()) {
                $stepsExecutionDto->setError(
                    Error::createInternalError($thresholdValidationResult->getOutputMessage() ?? 'Threshold validation error'),
                );

                break;
            }

            $releaseGroupValidateResult = $this->releaseGroupValidator->isValidReleaseGroup($releaseGroup);
            if (!$releaseGroupValidateResult->isSuccessful()) {
                $stepsExecutionDto->setError(
                    Error::createInternalError($releaseGroupValidateResult->getOutputMessage() ?? 'RG validation error'),
                );

                $stepsExecutionDto->setBlockerInfo((string)$releaseGroupValidateResult->getOutputMessage());

                break;
            }

            $aggregatedReleaseGroupCollection->add($releaseGroup);
        }

        if (!$this->dispatchEvent(new ReleaseGroupProcessorEvent($stepsExecutionDto), ReleaseGroupProcessorEvent::PRE_REQUIRE)) {
            return $stepsExecutionDto;
        }

        $response = $this->modulePackageFetcher->require($aggregatedReleaseGroupCollection->getCommonModuleCollection());
        $stepsExecutionDto->setIsSuccessful($response->isSuccessful());

        if (!$response->isSuccessful()) {
            $stepsExecutionDto->setError(
                Error::createClientCodeError($response->getOutputMessage() ?? 'Module fetcher error'),
            );
        }

        if ($response->isSuccessful() && $response->getOutputMessage() !== null) {
            $stepsExecutionDto->addOutputMessage($response->getOutputMessage());
        }

        if ($response->isSuccessful() && $aggregatedReleaseGroupCollection->count()) {
            $stepsExecutionDto->addOutputMessage(
                sprintf('Amount of applied release groups: %s', $aggregatedReleaseGroupCollection->count()),
            );
        }

        if (!$this->dispatchEvent(new ReleaseGroupProcessorPostRequireEvent($stepsExecutionDto, $response), ReleaseGroupProcessorPostRequireEvent::POST_REQUIRE)) {
            return $stepsExecutionDto;
        }

        if (!$this->dispatchEvent(new ReleaseGroupProcessorEvent($stepsExecutionDto), ReleaseGroupProcessorEvent::POST_PROCESSOR)) {
            return $stepsExecutionDto;
        }

        return $stepsExecutionDto;
    }
}
