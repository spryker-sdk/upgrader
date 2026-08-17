<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\EventSubscriber;

use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\ClassExtendsUpdatedPackageChecker;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorPostRequireEvent;

class ClassExtendsUpdatedPackageCheckerEventSubscriber implements EventSubscriberInterface
{
    protected ClassExtendsUpdatedPackageChecker $classExtendsUpdatedPackageChecker;

    protected ConfigurationProviderInterface $configurationProvider;

    public function __construct(
        ClassExtendsUpdatedPackageChecker $classExtendsUpdatedPackageChecker,
        ConfigurationProviderInterface $configurationProvider
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->classExtendsUpdatedPackageChecker = $classExtendsUpdatedPackageChecker;
    }

    /**
     * @return array<mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ReleaseGroupProcessorPostRequireEvent::POST_REQUIRE => 'onPostRequire',
        ];
    }

    public function onPostRequire(ReleaseGroupProcessorPostRequireEvent $event): void
    {
        if (!$this->configurationProvider->isEvaluatorEnabled()) {
            return;
        }

        $stepsExecutorDto = $event->getStepsExecutionDto();

        $violations = $this->classExtendsUpdatedPackageChecker->check();

        foreach ($violations as $violation) {
            $stepsExecutorDto->addViolation($violation);
        }
    }
}
