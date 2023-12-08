<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\PackagesSynchronizer;

use DynamicEvaluator\Application\PackagesSynchronizer\PackagesSynchronizerEventSubscriber;
use DynamicEvaluator\Application\PackagesSynchronizer\PackagesSynchronizerInterface;
use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;

class PackagesSynchronizerEventSubscriberTest extends TestCase
{
    /**
     * @return void
     */
    public function testOnPreProcessorShouldCallSynchronizer(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);
        $packagesSynchronizer->expects($this->once())->method('clear');

        $checkerExecutorEventSubscriber = new PackagesSynchronizerEventSubscriber(
            $packagesSynchronizer,
            $this->createConfigurationProviderMock(),
        );

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $checkerExecutorEventSubscriber->onPreProcessor($event);
    }

    /**
     * @return void
     */
    public function testOnPreProcessorShouldBeSkippedWhenEvaluatorDisabled(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);
        $packagesSynchronizer->expects($this->never())->method('clear');

        $checkerExecutorEventSubscriber = new PackagesSynchronizerEventSubscriber(
            $packagesSynchronizer,
            $this->createConfigurationProviderMock(false),
        );

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

        $checkerExecutorEventSubscriber = new PackagesSynchronizerEventSubscriber(
            $packagesSynchronizer,
            $this->createConfigurationProviderMock(),
        );

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $checkerExecutorEventSubscriber->onPreRequire($event);
    }

    /**
     * @return void
     */
    public function testOnPreRequireShouldBeSkippedWhenEvaluatorDisabled(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);
        $packagesSynchronizer->expects($this->never())->method('sync');

        $checkerExecutorEventSubscriber = new PackagesSynchronizerEventSubscriber(
            $packagesSynchronizer,
            $this->createConfigurationProviderMock(false),
        );

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

        $checkerExecutorEventSubscriber = new PackagesSynchronizerEventSubscriber(
            $packagesSynchronizer,
            $this->createConfigurationProviderMock(),
        );

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $checkerExecutorEventSubscriber->onPostProcessor($event);
    }

    /**
     * @return void
     */
    public function testOnPostProcessorShouldBeSkippedWhenEvaluatorDisabled(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);
        $packagesSynchronizer->expects($this->never())->method('clear');

        $checkerExecutorEventSubscriber = new PackagesSynchronizerEventSubscriber(
            $packagesSynchronizer,
            $this->createConfigurationProviderMock(false),
        );

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $checkerExecutorEventSubscriber->onPostProcessor($event);
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
