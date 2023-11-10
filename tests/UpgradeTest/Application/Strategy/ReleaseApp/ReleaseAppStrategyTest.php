<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\ReleaseApp;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Dto\ValidatorViolationDto;
use Upgrade\Application\Executor\StepExecutorInterface;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseAppStrategy;

class ReleaseAppStrategyTest extends TestCase
{
    /**
     * @return void
     */
    public function testUpgradeShouldNotSendEmptyPrWhenItIsSuccessful(): void
    {
        // Arrange & Assert
        $returnStepsResponse = new StepsResponseDto(true);
        $strategy = new ReleaseAppStrategy(
            $this->createStepExecutorMock($returnStepsResponse),
            $this->createErrorPrStepExecutorMock(false),
            $this->createLoggerMock(),
        );

        // Act
        $strategy->upgrade();
    }

    /**
     * @return void
     */
    public function testUpgradeShouldNotSendEmptyPrWhenNoErrors(): void
    {
        // Arrange & Assert
        $returnStepsResponse = new StepsResponseDto(false);
        $strategy = new ReleaseAppStrategy(
            $this->createStepExecutorMock($returnStepsResponse),
            $this->createErrorPrStepExecutorMock(false),
            $this->createLoggerMock(),
        );

        // Act
        $strategy->upgrade();
    }

    /**
     * @return void
     */
    public function testUpgradeShouldNotSendEmptyPrWhenPrIsSent(): void
    {
        // Arrange & Assert
        $returnStepsResponse = new StepsResponseDto(false);
        $returnStepsResponse->addBlocker(new ValidatorViolationDto('title', 'message'));
        $returnStepsResponse->setIsPullRequestSent(true);
        $strategy = new ReleaseAppStrategy(
            $this->createStepExecutorMock($returnStepsResponse),
            $this->createErrorPrStepExecutorMock(false),
            $this->createLoggerMock(),
        );

        // Act
        $strategy->upgrade();
    }

    /**
     * @return void
     */
    public function testUpgradeShouldSendEmptyPr(): void
    {
        // Arrange & Assert
        $returnStepsResponse = new StepsResponseDto(false);
        $returnStepsResponse->addBlocker(new ValidatorViolationDto('title', 'message'));
        $returnStepsResponse->setIsPullRequestSent(false);
        $strategy = new ReleaseAppStrategy(
            $this->createStepExecutorMock($returnStepsResponse),
            $this->createErrorPrStepExecutorMock(true),
            $this->createLoggerMock(),
        );

        // Act
        $strategy->upgrade();
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $returnStepsResponse
     *
     * @return \Upgrade\Application\Executor\StepExecutorInterface
     */
    protected function createStepExecutorMock(StepsResponseDto $returnStepsResponse): StepExecutorInterface
    {
        $stepExecutor = $this->createMock(StepExecutorInterface::class);
        $stepExecutor->method('execute')->willReturn($returnStepsResponse);

        return $stepExecutor;
    }

    /**
     * @param bool $shouldBeInvoked
     *
     * @return \Upgrade\Application\Executor\StepExecutorInterface
     */
    protected function createErrorPrStepExecutorMock(bool $shouldBeInvoked = true): StepExecutorInterface
    {
        $stepExecutor = $this->createMock(StepExecutorInterface::class);
        $stepExecutor->expects($shouldBeInvoked ? $this->once() : $this->never())->method('execute');

        return $stepExecutor;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    protected function createLoggerMock(): LoggerInterface
    {
        return $this->createMock(LoggerInterface::class);
    }
}
