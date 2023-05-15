<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\ReleaseApp\Processor\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Upgrade\Application\Checker\CheckerInterface;
use Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesSynchronizerInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Dto\ViolationDto;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;
use Upgrade\Application\Strategy\ReleaseApp\Processor\EventSubscriber\CheckerExecutorEventSubscriber;

class CheckerExecutorEventSubscriberTest extends TestCase
{
    /**
     * @return void
     */
    public function testOnPostRequireShouldAddViolationsIntoResponseDto(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);

        $checkerExecutorEventSubscriber = new CheckerExecutorEventSubscriber([
            $this->createCheckerMock(new ViolationDto('violation one')),
            $this->createCheckerMock(new ViolationDto('violation two')),
        ], $packagesSynchronizer);

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $checkerExecutorEventSubscriber->onPostRequire($event);

        // Assert
        $violations = $event->getStepsExecutionDto()->getViolations();
        $this->assertCount(2, $violations);
        $this->assertSame('violation one', $violations[0]->getMessage());
        $this->assertSame('violation two', $violations[1]->getMessage());
    }

    /**
     * @return void
     */
    public function testOnPreProcessorShouldCallSynchronizer(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);
        $packagesSynchronizer->expects($this->once())->method('clear');

        $checkerExecutorEventSubscriber = new CheckerExecutorEventSubscriber([
            $this->createCheckerMock(new ViolationDto('violation two')),
        ], $packagesSynchronizer);

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $checkerExecutorEventSubscriber->onPreProcessor($event);
    }

    /**
     * @return void
     */
    public function testOnPreRequireShouldCallSynchronizer(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);
        $packagesSynchronizer->expects($this->once())->method('sync');

        $checkerExecutorEventSubscriber = new CheckerExecutorEventSubscriber([
            $this->createCheckerMock(new ViolationDto('violation two')),
        ], $packagesSynchronizer);

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $checkerExecutorEventSubscriber->onPreRequire($event);
    }

    /**
     * @return void
     */
    public function testOnPostProcessorShouldCallSynchronizer(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);
        $packagesSynchronizer->expects($this->once())->method('clear');

        $checkerExecutorEventSubscriber = new CheckerExecutorEventSubscriber([
            $this->createCheckerMock(new ViolationDto('violation two')),
        ], $packagesSynchronizer);

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $checkerExecutorEventSubscriber->onPostProcessor($event);
    }

    /**
     * @param \Upgrade\Application\Dto\ViolationDto $violationDto
     *
     * @return \Upgrade\Application\Checker\CheckerInterface
     */
    public function createCheckerMock(ViolationDto $violationDto): CheckerInterface
    {
        $checker = $this->createMock(CheckerInterface::class);
        $checker->method('check')->willReturn([$violationDto]);

        return $checker;
    }
}
