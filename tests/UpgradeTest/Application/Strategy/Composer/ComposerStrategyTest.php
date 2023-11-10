<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\Composer;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Executor\StepExecutor;
use Upgrade\Application\Strategy\Composer\ComposerStrategy;
use Upgrade\Application\Strategy\FixerStepInterface;
use UpgradeData\Infrastructure\Processor\Strategy\Composer\Steps\FooRollbackStep;
use UpgradeData\Infrastructure\Processor\Strategy\Composer\Steps\FooStep;

class ComposerStrategyTest extends TestCase
{
    /**
     * @return void
     */
    public function testUpgradeWithoutSteps(): void
    {
        // Arrange
        $strategy = new ComposerStrategy(
            new StepExecutor($this->createMock(LoggerInterface::class)),
            new StepExecutor($this->createMock(LoggerInterface::class)),
            $this->createMock(LoggerInterface::class),
        );

        // Act
        $stepsExecutionDto = $strategy->upgrade();

        // Assert
        $this->assertTrue($stepsExecutionDto->getIsSuccessful());
        $this->assertNull($stepsExecutionDto->getOutputMessage());
        $this->assertNull($stepsExecutionDto->getComposerLockDiff());
        $this->assertNull($stepsExecutionDto->getPullRequestId());
    }

    /**
     * @return void
     */
    public function testUpgradeSuccessFlow(): void
    {
        // Arrange
        $stepsExecutionDto = new StepsResponseDto(true);
        $fooStep = $this->createMock(FooRollbackStep::class);
        $fooStep->method('run')->willReturn($stepsExecutionDto);
        $fooStep->expects($this->never())->method('rollBack');

        $strategy = new ComposerStrategy(
            new StepExecutor(
                $this->createMock(LoggerInterface::class),
                [
                $fooStep,
                new FooStep(),
                new FooRollbackStep(),
                ],
            ),
            new StepExecutor($this->createMock(LoggerInterface::class)),
            $this->createMock(LoggerInterface::class),
        );

        // Act
        $stepsExecutionDto = $strategy->upgrade();

        // Assert
        $this->assertTrue($stepsExecutionDto->getIsSuccessful());
        $this->assertNull($stepsExecutionDto->getComposerLockDiff());
        $this->assertNull($stepsExecutionDto->getPullRequestId());
    }

    /**
     * @return void
     */
    public function testUpgradeSuccessFlowWithStoppedPropagation(): void
    {
        // Arrange
        $stepsExecutionDto = new StepsResponseDto(true);
        $stepsExecutionDto->setIsStopPropagation(true);

        $fooStep = $this->createMock(FooRollbackStep::class);
        $fooStep->method('run')->willReturn($stepsExecutionDto);
        $fooStep->expects($this->never())->method('rollBack');

        $barStep = $this->createMock(FooRollbackStep::class);
        $barStep->expects($this->never())->method('run');
        $barStep->expects($this->never())->method('rollBack');

        $strategy = new ComposerStrategy(
            new StepExecutor(
                $this->createMock(LoggerInterface::class),
                [
                $fooStep,
                $barStep,
                ],
            ),
            new StepExecutor($this->createMock(LoggerInterface::class)),
            $this->createMock(LoggerInterface::class),
        );

        // Act
        $stepsExecutionDto = $strategy->upgrade();

        // Assert
        $this->assertTrue($stepsExecutionDto->getIsSuccessful());
        $this->assertNull($stepsExecutionDto->getComposerLockDiff());
        $this->assertNull($stepsExecutionDto->getPullRequestId());
    }

    /**
     * @return void
     */
    public function testUpgradeSuccessFlowWithFix(): void
    {
        // Arrange
        $stepsExecutionDto = new StepsResponseDto(false);
        $fooStep = $this->createMock(FooRollbackStep::class);
        $fooStep->method('run')->willReturn($stepsExecutionDto, new StepsResponseDto(true));
        $fooStep->expects($this->never())->method('rollBack');

        $fixerStep = $this->createMock(FixerStepInterface::class);
        $fixerStep
            ->method('isApplicable')
            ->with($stepsExecutionDto)
            ->willReturn(true);
        $fixerStep
            ->method('run')
            ->willReturn(new StepsResponseDto(true));

        $strategy = new ComposerStrategy(
            new StepExecutor(
                $this->createMock(LoggerInterface::class),
                [
                    $fooStep,
                    new FooStep(),
                    new FooRollbackStep(),
                ],
                [
                    $fixerStep,
                ],
            ),
            new StepExecutor($this->createMock(LoggerInterface::class)),
            $this->createMock(LoggerInterface::class),
        );

        // Act
        $stepsExecutionDto = $strategy->upgrade();

        // Assert
        $this->assertTrue($stepsExecutionDto->getIsSuccessful());
        $this->assertNull($stepsExecutionDto->getComposerLockDiff());
        $this->assertNull($stepsExecutionDto->getPullRequestId());
    }

    /**
     * @return void
     */
    public function testUpgradeUnSuccessFlow(): void
    {
        // Arrange
        $strategy = new ComposerStrategy(
            new StepExecutor(
                $this->createMock(LoggerInterface::class),
                [
                    new FooStep(),
                    $this->mockUnSuccessFooStep(),
                ],
            ),
            new StepExecutor($this->createMock(LoggerInterface::class)),
            $this->createMock(LoggerInterface::class),
        );

        // Act
        $stepsExecutionDto = $strategy->upgrade();

        // Assert
        $this->assertFalse($stepsExecutionDto->getIsSuccessful());
        $this->assertNull($stepsExecutionDto->getComposerLockDiff());
        $this->assertNull($stepsExecutionDto->getPullRequestId());
    }

    /**
     * @return void
     */
    public function testUpgradeUnSuccessFlowAndRollBack(): void
    {
        // Arrange
        $fooRollbackStep = $this->createMock(FooRollbackStep::class);
        $fooRollbackStep->expects($this->any())->method('rollBack');

        $strategy = new ComposerStrategy(
            new StepExecutor(
                $this->createMock(LoggerInterface::class),
                [
                    new FooStep(),
                    $fooRollbackStep,
                    $this->mockUnSuccessFooStep(),
                ],
            ),
            new StepExecutor($this->createMock(LoggerInterface::class)),
            $this->createMock(LoggerInterface::class),
        );

        // Act
        $stepsExecutionDto = $strategy->upgrade();

        // Assert
        $this->assertFalse($stepsExecutionDto->getIsSuccessful());
        $this->assertNull($stepsExecutionDto->getComposerLockDiff());
        $this->assertNull($stepsExecutionDto->getPullRequestId());
    }

    /**
     * @return \UpgradeData\Infrastructure\Processor\Strategy\Composer\Steps\FooStep
     */
    protected function mockUnSuccessFooStep(): FooStep
    {
        $stepsExecutionDto = new StepsResponseDto(false);

        $fooStep = $this->createMock(FooStep::class);
        $fooStep->method('run')->willReturn($stepsExecutionDto);

        return $fooStep;
    }
}
