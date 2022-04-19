<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace UpgradeTest\Infrastructure\Processor\Strategy\Composer;

use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\Step\StepsExecutionDto;
use Upgrade\Infrastructure\Processor\Strategy\Composer\ComposerStrategy;
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
        $strategy = new ComposerStrategy([]);

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
        $stepsExecutionDto = new StepsExecutionDto(true);
        $fooStep = $this->createMock(FooRollbackStep::class);
        $fooStep->method('run')->willReturn($stepsExecutionDto);
        $fooStep->expects($this->never())->method('rollBack');

        $strategy = new ComposerStrategy([
            $fooStep,
            new FooStep(),
            new FooRollbackStep(),
        ]);

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
    public function testUpgradeUnSuccessFlow(): void
    {
        // Arrange
        $strategy = new ComposerStrategy([
            new FooStep(),
            $this->mockUnSuccessFooStep(),
        ]);

        // Act
        $stepsExecutionDto = $strategy->upgrade();

        // Assert
        $this->assertFalse($stepsExecutionDto->getIsSuccessful());
        $this->assertNull($stepsExecutionDto->getOutputMessage());
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

        $strategy = new ComposerStrategy([
            new FooStep(),
            $fooRollbackStep,
            $this->mockUnSuccessFooStep(),
        ]);

        // Act
        $stepsExecutionDto = $strategy->upgrade();

        // Assert
        $this->assertFalse($stepsExecutionDto->getIsSuccessful());
        $this->assertNull($stepsExecutionDto->getOutputMessage());
        $this->assertNull($stepsExecutionDto->getComposerLockDiff());
        $this->assertNull($stepsExecutionDto->getPullRequestId());
    }

    /**
     * @return \Fixtures\Infrastructure\Processor\Strategy\Composer\Steps\FooStep
     */
    protected function mockUnSuccessFooStep(): FooStep
    {
        $stepsExecutionDto = new StepsExecutionDto(false);

        $fooStep = $this->createMock(FooStep::class);
        $fooStep->method('run')->willReturn($stepsExecutionDto);

        return $fooStep;
    }
}
