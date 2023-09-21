<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\ModuleNameConflictChecker\EventSubscriber;

use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Dto\ProjectModulesStateDto;
use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Dto\ViolationDto;
use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\EventSubscriber\ModuleNameConflictCheckerEventSubscriber;
use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Fetcher\ComposerModulesNamesFetcherInterface;
use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Fetcher\ProjectModulesNamesFetcherInterface;
use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\PreviousProjectModulesStateStorage\PreviousProjectModulesStateStorage;
use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorPostRequireEvent;

class ModuleNameConflictCheckerEventSubscriberTest extends TestCase
{
    /**
     * @return void
     */
    public function testOnPreRequireShouldBeSkippedWhenEvaluatorDisabled(): void
    {
        // Arrange
        $previousModuleStateStorage = new PreviousProjectModulesStateStorage();

        $eventSubscriber = new ModuleNameConflictCheckerEventSubscriber(
            $this->createComposerModulesNamesFetcherMock([]),
            $this->createProjectModulesNamesFetcherMock([]),
            $this->createConfigurationProviderMock(false),
            $previousModuleStateStorage,
        );

        // Act
        $eventSubscriber->onPreRequire(new ReleaseGroupProcessorEvent(new StepsResponseDto()));

        // Assert
        $this->assertNull($previousModuleStateStorage->getPreviousProjectModulesState());
    }

    /**
     * @return void
     */
    public function testOnPreRequireShouldSetReturnInitialState(): void
    {
        // Arrange
        $previousModuleStateStorage = new PreviousProjectModulesStateStorage();

        $eventSubscriber = new ModuleNameConflictCheckerEventSubscriber(
            $this->createComposerModulesNamesFetcherMock(['one']),
            $this->createProjectModulesNamesFetcherMock(['two']),
            $this->createConfigurationProviderMock(),
            $previousModuleStateStorage,
        );

        // Act
        $eventSubscriber->onPreRequire(new ReleaseGroupProcessorEvent(new StepsResponseDto()));

        // Assert
        $previousModuleState = $previousModuleStateStorage->getRequiredPreviousProjectModulesState();

        $this->assertSame(['one'], $previousModuleState->getComposerInstalledModules());
        $this->assertSame(['two'], $previousModuleState->getProjectModules());
    }

    /**
     * @return void
     */
    public function testOnPostRequireShouldBeSkippedWhenEvaluatorDisabled(): void
    {
        // Arrange
        $previousModuleStateStorage = new PreviousProjectModulesStateStorage();

        $eventSubscriber = new ModuleNameConflictCheckerEventSubscriber(
            $this->createComposerModulesNamesFetcherMock([]),
            $this->createProjectModulesNamesFetcherMock([]),
            $this->createConfigurationProviderMock(false),
            $previousModuleStateStorage,
        );

        // Act
        $stepsResponse = new StepsResponseDto();
        $eventSubscriber->onPostRequire(new ReleaseGroupProcessorPostRequireEvent($stepsResponse, new PackageManagerResponseDto(true)));

        // Assert
        $this->assertEmpty($stepsResponse->getViolations());
    }

    /**
     * @return void
     */
    public function testOnPostRequireShouldReturnNoViolationsWhenNoInterceptionsFound(): void
    {
        // Arrange
        $previousModuleStateStorage = new PreviousProjectModulesStateStorage();
        $previousModuleStateStorage->setPreviousProjectModulesState(new ProjectModulesStateDto(
            ['moduleOne'],
            ['moduleOne'],
        ));

        $eventSubscriber = new ModuleNameConflictCheckerEventSubscriber(
            $this->createComposerModulesNamesFetcherMock(['moduleOne', 'moduleTwo']),
            $this->createProjectModulesNamesFetcherMock([]),
            $this->createConfigurationProviderMock(false),
            $previousModuleStateStorage,
        );

        // Act
        $stepsResponse = new StepsResponseDto();
        $eventSubscriber->onPostRequire(new ReleaseGroupProcessorPostRequireEvent($stepsResponse, new PackageManagerResponseDto(true)));

        // Assert
        $this->assertEmpty($stepsResponse->getViolations());
    }

    /**
     * @return void
     */
    public function testOnPostRequireShouldReturnViolationsWhenInterceptionsFound(): void
    {
        // Arrange
        $previousModuleStateStorage = new PreviousProjectModulesStateStorage();
        $previousModuleStateStorage->setPreviousProjectModulesState(new ProjectModulesStateDto(
            ['moduleOne', 'moduleTwo'],
            ['moduleOne'],
        ));

        $eventSubscriber = new ModuleNameConflictCheckerEventSubscriber(
            $this->createComposerModulesNamesFetcherMock(['moduleOne', 'moduleTwo']),
            $this->createProjectModulesNamesFetcherMock([]),
            $this->createConfigurationProviderMock(),
            $previousModuleStateStorage,
        );

        // Act
        $stepsResponse = new StepsResponseDto();
        $eventSubscriber->onPostRequire(new ReleaseGroupProcessorPostRequireEvent($stepsResponse, new PackageManagerResponseDto(true)));

        // Assert
        $violations = $stepsResponse->getViolations();

        $this->assertCount(1, $violations);

        /** @var \DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Dto\ViolationDto $violation */
        $violation = $violations[0][0];
        $this->assertInstanceOf(ViolationDto::class, $violation);
        $this->assertEquals(['moduleTwo'], $violation->getExistingModules());
    }

    /**
     * @return void
     */
    public function testGetSubscribedEventsShouldReturnArrayOfEvents(): void
    {
        // Act
        $events = ModuleNameConflictCheckerEventSubscriber::getSubscribedEvents();

        // Assert
        $this->assertSame([ReleaseGroupProcessorEvent::PRE_REQUIRE, ReleaseGroupProcessorPostRequireEvent::POST_REQUIRE], array_keys($events));
    }

    /**
     * @param array<string> $moduleNames
     *
     * @return \DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Fetcher\ComposerModulesNamesFetcherInterface
     */
    protected function createComposerModulesNamesFetcherMock(array $moduleNames): ComposerModulesNamesFetcherInterface
    {
        $composerModulesNamesFetcher = $this->createMock(ComposerModulesNamesFetcherInterface::class);
        $composerModulesNamesFetcher->method('fetchComposerModules')->willReturn($moduleNames);

        return $composerModulesNamesFetcher;
    }

    /**
     * @param array<string> $moduleNames
     *
     * @return \DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Fetcher\ProjectModulesNamesFetcherInterface
     */
    protected function createProjectModulesNamesFetcherMock(array $moduleNames): ProjectModulesNamesFetcherInterface
    {
        $projectModulesNamesFetcher = $this->createMock(ProjectModulesNamesFetcherInterface::class);
        $projectModulesNamesFetcher->method('fetchProjectModules')->willReturn($moduleNames);

        return $projectModulesNamesFetcher;
    }

    /**
     * @param bool $isEvaluatorEnabled
     *
     * @return \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected function createConfigurationProviderMock(bool $isEvaluatorEnabled = true): ConfigurationProviderInterface
    {
        $configurationProvider = $this->createMock(ConfigurationProviderInterface::class);
        $configurationProvider->method('isEvaluatorEnabled')->willReturn($isEvaluatorEnabled);

        return $configurationProvider;
    }
}
