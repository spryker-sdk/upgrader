<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\EventSubscriber;

use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\DbSchemaConflictChecker;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorPostRequireEvent;

class DbSchemaConflictCheckerEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\DbSchemaConflictChecker
     */
    protected DbSchemaConflictChecker $dbSchemaConflictChecker;

    /**
     * @var \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @param \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\DbSchemaConflictChecker $dbSchemaConflictChecker
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     */
    public function __construct(
        DbSchemaConflictChecker $dbSchemaConflictChecker,
        ConfigurationProviderInterface $configurationProvider
    ) {
        $this->dbSchemaConflictChecker = $dbSchemaConflictChecker;
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ReleaseGroupProcessorPostRequireEvent::POST_REQUIRE => 'onPostRequire',
        ];
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

        $violations = $this->dbSchemaConflictChecker->check();

        foreach ($violations as $violation) {
            $stepsExecutorDto->addViolation($violation);
        }
    }
}
