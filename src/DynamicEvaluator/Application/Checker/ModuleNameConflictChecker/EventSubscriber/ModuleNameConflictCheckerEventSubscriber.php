<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\EventSubscriber;

use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Dto\ProjectModulesStateDto;
use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Dto\ViolationDto;
use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Fetcher\ComposerModulesNamesFetcherInterface;
use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Fetcher\ProjectModulesNamesFetcherInterface;
use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\PreviousProjectModulesStateStorage\PreviousProjectModulesStateStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorPostRequireEvent;

class ModuleNameConflictCheckerEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var int
     */
    protected const BEFORE_INTEGRATOR_PRIORITY = 200;

    /**
     * @var \DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Fetcher\ComposerModulesNamesFetcherInterface
     */
    protected ComposerModulesNamesFetcherInterface $composerModulesNamesFetcher;

    /**
     * @var \DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Fetcher\ProjectModulesNamesFetcherInterface
     */
    protected ProjectModulesNamesFetcherInterface $projectModulesNamesFetcher;

    /**
     * @var \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @var \DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\PreviousProjectModulesStateStorage\PreviousProjectModulesStateStorageInterface
     */
    protected PreviousProjectModulesStateStorageInterface $previousProjectModulesStateStorage;

    /**
     * @param \DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Fetcher\ComposerModulesNamesFetcherInterface $composerModulesNamesFetcher
     * @param \DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Fetcher\ProjectModulesNamesFetcherInterface $projectModulesNamesFetcher
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     * @param \DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\PreviousProjectModulesStateStorage\PreviousProjectModulesStateStorageInterface $previousProjectModulesStateStorage
     */
    public function __construct(
        ComposerModulesNamesFetcherInterface $composerModulesNamesFetcher,
        ProjectModulesNamesFetcherInterface $projectModulesNamesFetcher,
        ConfigurationProviderInterface $configurationProvider,
        PreviousProjectModulesStateStorageInterface $previousProjectModulesStateStorage
    ) {
        $this->composerModulesNamesFetcher = $composerModulesNamesFetcher;
        $this->projectModulesNamesFetcher = $projectModulesNamesFetcher;
        $this->configurationProvider = $configurationProvider;
        $this->previousProjectModulesStateStorage = $previousProjectModulesStateStorage;
    }

    /**
     * @return array<mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ReleaseGroupProcessorEvent::PRE_REQUIRE => ['onPreRequire', static::BEFORE_INTEGRATOR_PRIORITY],
            ReleaseGroupProcessorPostRequireEvent::POST_REQUIRE => ['onPostRequire', static::BEFORE_INTEGRATOR_PRIORITY],
        ];
    }

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent $event
     *
     * @return void
     */
    public function onPreRequire(ReleaseGroupProcessorEvent $event): void
    {
        if (!$this->configurationProvider->isEvaluatorEnabled()) {
            return;
        }

        $this->previousProjectModulesStateStorage->setPreviousProjectModulesState(
            new ProjectModulesStateDto(
                $this->projectModulesNamesFetcher->fetchProjectModules(),
                $this->composerModulesNamesFetcher->fetchComposerModules(),
            ),
        );
    }

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorPostRequireEvent $event
     *
     * @return void
     */
    public function onPostRequire(ReleaseGroupProcessorPostRequireEvent $event): void
    {
        if (!$this->configurationProvider->isEvaluatorEnabled()) {
            return;
        }

        $previousProjectModulesState = $this->previousProjectModulesStateStorage->getRequiredPreviousProjectModulesState();

        $newComposerModules = array_diff(
            $this->composerModulesNamesFetcher->fetchComposerModules(),
            $previousProjectModulesState->getComposerInstalledModules(),
        );

        $alreadyExistingModules = array_intersect(
            $previousProjectModulesState->getProjectModules(),
            $newComposerModules,
        );

        if (count($alreadyExistingModules) === 0) {
            return;
        }

        $stepsExecutorDto = $event->getStepsExecutionDto();
        $composerCommands = $event->getPackageManagerResponseDto()->getExecutedCommands();

        $stepsExecutorDto->addViolation(
            new ViolationDto($alreadyExistingModules, $composerCommands),
        );
    }
}
