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

class SequentialReleaseGroupProcessor extends BaseReleaseGroupProcessor
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
     * @param \Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager
     * @param \Upgrade\Application\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface $thresholdSoftValidator
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcher $moduleFetcher
     * @param \Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\ReleaseGroupFilterInterface $releaseGroupFilter
     * @param \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager,
        ThresholdSoftValidatorInterface $thresholdSoftValidator,
        ModuleFetcher $moduleFetcher,
        ReleaseGroupFilterInterface $releaseGroupFilter,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($eventDispatcher);

        $this->releaseGroupValidator = $releaseGroupValidateManager;
        $this->thresholdValidator = $thresholdSoftValidator;
        $this->moduleFetcher = $moduleFetcher;
        $this->releaseGroupPackageFilter = $releaseGroupFilter;
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

        if (!$this->dispatchEvent(new ReleaseGroupProcessorEvent($stepsExecutionDto), ReleaseGroupProcessorEvent::PRE_PROCESSOR)) {
            return $stepsExecutionDto;
        }

        $aggregatedReleaseGroupCollection = new ReleaseGroupDtoCollection();
        foreach ($requireRequestCollection->toArray() as $releaseGroup) {
            $releaseGroup = $this->releaseGroupPackageFilter->filter($releaseGroup);
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

            $validateResult = $this->releaseGroupValidator->isValidReleaseGroup($releaseGroup);
            if (!$validateResult->isSuccessful()) {
                $stepsExecutionDto->setError(
                    Error::createInternalError($validateResult->getOutputMessage() ?? 'RG validation error'),
                );
                $stepsExecutionDto->setBlockerInfo((string)$validateResult->getOutputMessage());

                break;
            }

            if (!$this->dispatchEvent(new ReleaseGroupProcessorEvent($stepsExecutionDto), ReleaseGroupProcessorEvent::PRE_REQUIRE)) {
                break;
            }

            $response = $this->moduleFetcher->require($releaseGroup->getModuleCollection());

            $this->addReleaseGroupStat($stepsExecutionDto, $response);

            if ($response->isSuccessful() && $response->getOutputMessage() !== null) {
                $stepsExecutionDto->addOutputMessage($response->getOutputMessage());
            }

            if (!$response->isSuccessful()) {
                $stepsExecutionDto->setIsSuccessful(false);

                $stepsExecutionDto->setError(
                    Error::createClientCodeError($response->getOutputMessage() ?? 'Module fetcher error'),
                );

                break;
            }

            $stepsExecutionDto->setLastAppliedReleaseGroup($releaseGroup);
            $aggregatedReleaseGroupCollection->add($releaseGroup);

            if (!$this->dispatchEvent(new ReleaseGroupProcessorPostRequireEvent($stepsExecutionDto, $response), ReleaseGroupProcessorPostRequireEvent::POST_REQUIRE)) {
                break;
            }
        }

        if (!$this->dispatchEvent(new ReleaseGroupProcessorEvent($stepsExecutionDto), ReleaseGroupProcessorEvent::POST_PROCESSOR)) {
            return $stepsExecutionDto;
        }

        if ($aggregatedReleaseGroupCollection->count()) {
            $this->addAppliedRGsInfo(
                $stepsExecutionDto,
                $aggregatedReleaseGroupCollection->count(),
                $aggregatedReleaseGroupCollection->getSecurityFixes()->count(),
            );
        }

        return $stepsExecutionDto;
    }
}
