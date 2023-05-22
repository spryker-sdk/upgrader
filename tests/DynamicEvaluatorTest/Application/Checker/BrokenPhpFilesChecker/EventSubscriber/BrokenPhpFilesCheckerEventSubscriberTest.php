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
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher\FileErrorsFetcherInterface;
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
        $fileErrorsFetcherMock = $this->createMock(FileErrorsFetcherInterface::class);
        $fileErrorsFetcherMock->expects($this->once())->method('reset');
        $fileErrorsFetcherMock->expects($this->once())->method('fetchNewProjectFileErrors');

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        $brokenPhpFilesCheckerEventSubscriber = new BrokenPhpFilesCheckerEventSubscriber(
            $this->createConfigurationProviderMock(),
            $fileErrorsFetcherMock,
            $this->createBrokenPhpFilesCheckerMock([]),
        );

        // Act
        $brokenPhpFilesCheckerEventSubscriber->onPreProcessor($event);
    }

    /**
     * @return void
     */
    public function testOnPreProcessorShouldShouldCkipExecutionWhenEvaluatorDisabled(): void
    {
        // Arrange
        $fileErrorsFetcherMock = $this->createMock(FileErrorsFetcherInterface::class);
        $fileErrorsFetcherMock->expects($this->never())->method('reset');
        $fileErrorsFetcherMock->expects($this->never())->method('fetchNewProjectFileErrors');

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        $brokenPhpFilesCheckerEventSubscriber = new BrokenPhpFilesCheckerEventSubscriber(
            $this->createConfigurationProviderMock(false),
            $fileErrorsFetcherMock,
            $this->createBrokenPhpFilesCheckerMock([]),
        );

        // Act
        $brokenPhpFilesCheckerEventSubscriber->onPreProcessor($event);
    }

    /**
     * @return void
     */
    public function testOnPostRequireShouldAddViolation(): void
    {
        // Arrange
        $fileErrorsFetcherMock = $this->createMock(FileErrorsFetcherInterface::class);

        $event = new ReleaseGroupProcessorPostRequireEvent(new StepsResponseDto(), new PackageManagerResponseDto(true));

        $violations = [new ViolationDto([], [new FileErrorDto('src/someClass.php', 1, 'test message')])];

        $brokenPhpFilesCheckerEventSubscriber = new BrokenPhpFilesCheckerEventSubscriber(
            $this->createConfigurationProviderMock(),
            $fileErrorsFetcherMock,
            $this->createBrokenPhpFilesCheckerMock($violations),
        );

        // Act
        $brokenPhpFilesCheckerEventSubscriber->onPostRequire($event);

        // Assert
        $this->assertSame($violations, $event->getStepsExecutionDto()->getViolations());
    }

    /**
     * @return void
     */
    public function testOnPostRequireShouldNotInvokeCheckerWhenItDisabled(): void
    {
        // Arrange
        $fileErrorsFetcherMock = $this->createMock(FileErrorsFetcherInterface::class);

        $event = new ReleaseGroupProcessorPostRequireEvent(new StepsResponseDto(), new PackageManagerResponseDto(true));

        $brokenPhpFilesCheckerMock = $this->createMock(BrokenPhpFilesChecker::class);
        $brokenPhpFilesCheckerMock->expects($this->never())->method('check');

        $brokenPhpFilesCheckerEventSubscriber = new BrokenPhpFilesCheckerEventSubscriber(
            $this->createConfigurationProviderMock(false),
            $fileErrorsFetcherMock,
            $brokenPhpFilesCheckerMock,
        );

        // Act
        $brokenPhpFilesCheckerEventSubscriber->onPostRequire($event);
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

    /**
     * @param array<\DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\ViolationDto> $violations
     *
     * @return \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\BrokenPhpFilesChecker
     */
    public function createBrokenPhpFilesCheckerMock(array $violations): BrokenPhpFilesChecker
    {
        $brokenPhpFilesChecker = $this->createMock(BrokenPhpFilesChecker::class);
        $brokenPhpFilesChecker->method('check')->willReturn($violations);

        return $brokenPhpFilesChecker;
    }
}
