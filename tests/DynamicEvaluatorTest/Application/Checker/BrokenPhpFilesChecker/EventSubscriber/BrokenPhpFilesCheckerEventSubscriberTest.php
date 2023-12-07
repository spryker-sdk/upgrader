<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\BrokenPhpFilesChecker\EventSubscriber;

use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\BrokenPhpFilesChecker;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\ViolationDto;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\EventSubscriber\BrokenPhpFilesCheckerEventSubscriber;
use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorPostRequireEvent;

class BrokenPhpFilesCheckerEventSubscriberTest extends TestCase
{
    /**
     * @return void
     */
    public function testOnPreProcessorShouldInvokeFileFetcherMethods(): void
    {
        // Arrange
        $brokenPhpFilesCheckerMock = $this->createMock(BrokenPhpFilesChecker::class);
        $brokenPhpFilesCheckerMock->expects($this->once())->method('fetchAndPersistInitialErrors');

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        $brokenPhpFilesCheckerEventSubscriber = new BrokenPhpFilesCheckerEventSubscriber(
            $this->createConfigurationProviderMock(),
            $brokenPhpFilesCheckerMock,
        );

        // Act
        $brokenPhpFilesCheckerEventSubscriber->onPreProcessor($event);
    }

    /**
     * @return void
     */
    public function testOnPreProcessorShouldShouldSkipExecutionWhenEvaluatorDisabled(): void
    {
        // Arrange
        $brokenPhpFilesCheckerMock = $this->createMock(BrokenPhpFilesChecker::class);
        $brokenPhpFilesCheckerMock->expects($this->never())->method('fetchAndPersistInitialErrors');

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        $brokenPhpFilesCheckerEventSubscriber = new BrokenPhpFilesCheckerEventSubscriber(
            $this->createConfigurationProviderMock(false),
            $brokenPhpFilesCheckerMock,
        );

        // Act
        $brokenPhpFilesCheckerEventSubscriber->onPreProcessor($event);
    }

    /**
     * @return void
     */
    public function testOnPreRequireShouldInvokeFetchAndPersistInstalledSprykerModules(): void
    {
        // Arrange
        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        $brokenPhpFilesCheckerMock = $this->createMock(BrokenPhpFilesChecker::class);
        $brokenPhpFilesCheckerMock->expects($this->once())->method('fetchAndPersistInstalledSprykerModules');

        $brokenPhpFilesCheckerEventSubscriber = new BrokenPhpFilesCheckerEventSubscriber(
            $this->createConfigurationProviderMock(),
            $brokenPhpFilesCheckerMock,
        );

        // Act
        $brokenPhpFilesCheckerEventSubscriber->onPreRequire($event);
    }

    /**
     * @return void
     */
    public function testOnPreRequireShouldNotInvokeCheckerWhenItDisabled(): void
    {
        // Arrange
        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        $brokenPhpFilesCheckerMock = $this->createMock(BrokenPhpFilesChecker::class);
        $brokenPhpFilesCheckerMock->expects($this->never())->method('fetchAndPersistInstalledSprykerModules');

        $brokenPhpFilesCheckerEventSubscriber = new BrokenPhpFilesCheckerEventSubscriber(
            $this->createConfigurationProviderMock(false),
            $brokenPhpFilesCheckerMock,
        );

        // Act
        $brokenPhpFilesCheckerEventSubscriber->onPreRequire($event);
    }

    /**
     * @return void
     */
    public function testOnPostRequireShouldAddViolation(): void
    {
        // Arrange
        $violations = [new ViolationDto([], [new FileErrorDto('src/someClass.php', 1, 'test message')])];

        $brokenPhpFilesCheckerMock = $this->createMock(BrokenPhpFilesChecker::class);
        $brokenPhpFilesCheckerMock
            ->expects($this->once())
            ->method('checkUpdatedSprykerModules')
            ->willReturn($violations);

        $event = new ReleaseGroupProcessorPostRequireEvent(new StepsResponseDto(), new PackageManagerResponseDto(true));

        $brokenPhpFilesCheckerEventSubscriber = new BrokenPhpFilesCheckerEventSubscriber(
            $this->createConfigurationProviderMock(),
            $brokenPhpFilesCheckerMock,
        );

        // Act
        $brokenPhpFilesCheckerEventSubscriber->onPostRequire($event);

        // Assert
        $this->assertSame($violations, $event->getStepsExecutionDto()->getViolations()[0]);
    }

    /**
     * @return void
     */
    public function testOnPostRequireShouldNotInvokeCheckerWhenItDisabled(): void
    {
        // Arrange
        $event = new ReleaseGroupProcessorPostRequireEvent(new StepsResponseDto(), new PackageManagerResponseDto(true));

        $brokenPhpFilesCheckerMock = $this->createMock(BrokenPhpFilesChecker::class);
        $brokenPhpFilesCheckerMock->expects($this->never())->method('checkUpdatedSprykerModules');

        $brokenPhpFilesCheckerEventSubscriber = new BrokenPhpFilesCheckerEventSubscriber(
            $this->createConfigurationProviderMock(false),
            $brokenPhpFilesCheckerMock,
        );

        // Act
        $brokenPhpFilesCheckerEventSubscriber->onPostRequire($event);
    }

    /**
     * @return void
     */
    public function testOnPostProcessorShouldAddViolations(): void
    {
        // Arrange
        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());
        $violations = [new ViolationDto([], [new FileErrorDto('src/someClass.php', 1, 'test message')])];

        $brokenPhpFilesCheckerMock = $this->createMock(BrokenPhpFilesChecker::class);
        $brokenPhpFilesCheckerMock
            ->expects($this->once())
            ->method('checkAll')
            ->willReturn($violations);

        $brokenPhpFilesCheckerEventSubscriber = new BrokenPhpFilesCheckerEventSubscriber(
            $this->createConfigurationProviderMock(),
            $brokenPhpFilesCheckerMock,
        );

        // Act
        $brokenPhpFilesCheckerEventSubscriber->onPostProcessor($event);
    }

    /**
     * @return void
     */
    public function testOnPostProcessorShouldNotInvokeCheckerWhenItDisabled(): void
    {
        // Arrange
        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        $brokenPhpFilesCheckerMock = $this->createMock(BrokenPhpFilesChecker::class);
        $brokenPhpFilesCheckerMock->expects($this->never())->method('checkAll');

        $brokenPhpFilesCheckerEventSubscriber = new BrokenPhpFilesCheckerEventSubscriber(
            $this->createConfigurationProviderMock(false),
            $brokenPhpFilesCheckerMock,
        );

        // Act
        $brokenPhpFilesCheckerEventSubscriber->onPostProcessor($event);
    }

    /**
     * @param bool $isEvaluatorEnabled
     *
     * @return \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    public function createConfigurationProviderMock(bool $isEvaluatorEnabled = true): ConfigurationProviderInterface
    {
        $configurationProvider = $this->createMock(ConfigurationProviderInterface::class);
        $configurationProvider->method('isEvaluatorEnabled')->willReturn($isEvaluatorEnabled);

        return $configurationProvider;
    }
}
