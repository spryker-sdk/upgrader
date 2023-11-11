<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor;

use Psr\Log\LoggerInterface;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Factory\ComposerViolationDtoFactory;
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
     * @var \Upgrade\Application\Factory\ComposerViolationDtoFactory
     */
    protected ComposerViolationDtoFactory $composerViolationDtoFactory;

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager
     * @param \Upgrade\Application\Strategy\ReleaseApp\Validator\ThresholdSoftValidatorInterface $thresholdSoftValidator
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcher $moduleFetcher
     * @param \Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter\ReleaseGroupFilterInterface $releaseGroupFilter
     * @param \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Upgrade\Application\Factory\ComposerViolationDtoFactory $composerViolationDtoFactory
     */
    public function __construct(
        ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager,
        ThresholdSoftValidatorInterface $thresholdSoftValidator,
        ModuleFetcher $moduleFetcher,
        ReleaseGroupFilterInterface $releaseGroupFilter,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        ComposerViolationDtoFactory $composerViolationDtoFactory
    ) {
        parent::__construct($eventDispatcher, $logger);

        $this->releaseGroupValidator = $releaseGroupValidateManager;
        $this->thresholdValidator = $thresholdSoftValidator;
        $this->moduleFetcher = $moduleFetcher;
        $this->releaseGroupPackageFilter = $releaseGroupFilter;
        $this->composerViolationDtoFactory = $composerViolationDtoFactory;
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
            $this->logger->debug(sprintf('Release group `%s` is processing', $releaseGroup->getId()));

            $filterResponse = $this->releaseGroupPackageFilter->filter($releaseGroup);
            $stepsExecutionDto->addFilterResponse($filterResponse);
            $releaseGroup = $filterResponse->getReleaseGroupDto();
            if ($releaseGroup->getModuleCollection()->isEmpty()) {
                $this->logger->debug(sprintf('Release group `%s` is skipped', $releaseGroup->getId()));

                continue;
            }

            $stepsExecutionDto->setCurrentReleaseGroupId($releaseGroup->getId());

            $thresholdValidationResult = $this->thresholdValidator->validate($aggregatedReleaseGroupCollection);
            if (!$thresholdValidationResult->isSuccessful()) {
                $stepsExecutionDto->setError(
                    Error::createInternalError($thresholdValidationResult->getOutputMessage() ?? 'Threshold validation error'),
                );
                $this->logger->debug(sprintf(
                    'Release group `%s` is skipped by threshold, message: %s',
                    $releaseGroup->getId(),
                    $thresholdValidationResult->getOutputMessage(),
                ));

                break;
            }

            $validatorViolation = $this->releaseGroupValidator->isValidReleaseGroup($releaseGroup);
            if ($validatorViolation !== null) {
                $stepsExecutionDto->setError(
                    Error::createInternalError($validatorViolation->getMessage()),
                );
                $stepsExecutionDto->addBlocker($validatorViolation);
                $this->logger->debug(sprintf(
                    'Release group `%s` is skipped by validator, message: %s',
                    $releaseGroup->getId(),
                    $validatorViolation->getMessage(),
                ));

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
                $composerViolation = $this->composerViolationDtoFactory->createFromPackageManagerResponse($response);

                $stepsExecutionDto->setError(
                    Error::createClientCodeError($composerViolation->getMessage()),
                );
                $this->logger->debug(sprintf(
                    'Release group `%s` applying failed, message: %s',
                    $releaseGroup->getId(),
                    $response->getOutputMessage(),
                ));

                $stepsExecutionDto->addBlocker($composerViolation);

                break;
            }

            $stepsExecutionDto->addAppliedReleaseGroup($releaseGroup);
            $aggregatedReleaseGroupCollection->add($releaseGroup);
            $this->logger->debug(sprintf('Release group `%s` is processed', $releaseGroup->getId()));

            if (!$this->dispatchEvent(new ReleaseGroupProcessorPostRequireEvent($stepsExecutionDto, $response), ReleaseGroupProcessorPostRequireEvent::POST_REQUIRE)) {
                break;
            }
        }

        $this->logger->debug(sprintf('Release group processor is finished, applied release groups: %s', $aggregatedReleaseGroupCollection->count()));

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
