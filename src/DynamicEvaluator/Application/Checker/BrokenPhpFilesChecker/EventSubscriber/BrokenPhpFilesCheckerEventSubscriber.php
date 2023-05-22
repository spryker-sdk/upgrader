<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\EventSubscriber;

use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\BrokenPhpFilesChecker;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher\FileErrorsFetcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorPostRequireEvent;

class BrokenPhpFilesCheckerEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @var \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher\FileErrorsFetcherInterface
     */
    protected FileErrorsFetcherInterface $fileErrorsFetcher;

    /**
     * @var \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\BrokenPhpFilesChecker
     */
    protected BrokenPhpFilesChecker $checker;

    /**
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     * @param \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher\FileErrorsFetcherInterface $fileErrorsFetcher
     * @param \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\BrokenPhpFilesChecker $checker
     */
    public function __construct(
        ConfigurationProviderInterface $configurationProvider,
        FileErrorsFetcherInterface $fileErrorsFetcher,
        BrokenPhpFilesChecker $checker
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->fileErrorsFetcher = $fileErrorsFetcher;
        $this->checker = $checker;
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ReleaseGroupProcessorEvent::PRE_PROCESSOR => 'onPreProcessor',
            ReleaseGroupProcessorPostRequireEvent::POST_REQUIRE => 'onPostRequire',
        ];
    }

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent $event
     *
     * @return void
     */
    public function onPreProcessor(ReleaseGroupProcessorEvent $event): void
    {
        if (!$this->configurationProvider->isEvaluatorEnabled()) {
            return;
        }

        $this->fileErrorsFetcher->reset();
        $this->fileErrorsFetcher->fetchNewProjectFileErrors();
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

        $stepsExecutorDto = $event->getStepsExecutionDto();

        $violations = $this->checker->check($event->getPackageManagerResponseDto()->getExecutedCommands());

        foreach ($violations as $violation) {
            $stepsExecutorDto->addViolation($violation);
        }
    }
}
