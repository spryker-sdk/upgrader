<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace UpgradeTest\Infrastructure\Processor\Strategy\Composer\Steps;

use Core\Infrastructure\Service\ProcessRunnerService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Upgrade\Application\Dto\ComposerLockDiffDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\Common\Step\ComposerLockComparatorStep;
use Upgrade\Application\Strategy\Comparator\ComposerLockComparator;

class ComposerLockComparatorStepTest extends TestCase
{
    /**
     * @return void
     */
    public function testRunSuccessCase(): void
    {
        $this->markTestSkipped('TODO fix the test');

        // Arrange
        $processOutput = '{"changes":{"spryker\/product-label":["3.2.0","3.3.0","https:\/\/github.com\/spryker\/product-label\/compare\/3.2.0...3.3.0"]},"changes-dev":{"spryker-shop\/web-profiler-widget":["1.4.1","1.4.2","https:\/\/github.com\/spryker-shop\/web-profiler-widget\/compare\/1.4.1...1.4.2"]}}';
        $processRunnerMock = $this->mockProcessRunnerWithOutput($processOutput);
        $composerLockComparator = new ComposerLockComparator($processRunnerMock);
        $comparatorStep = new ComposerLockComparatorStep($composerLockComparator);

        // Act
        $stepsExecutionDto = $comparatorStep->run((new StepsResponseDto(true)));

        // Assert
        $this->assertTrue($stepsExecutionDto->getIsSuccessful());
        $this->assertNull($stepsExecutionDto->getOutputMessage());
        $this->assertNull($stepsExecutionDto->getPullRequestId());

        $composerLockDiffDto = $stepsExecutionDto->getComposerLockDiff();
        $this->assertInstanceOf(ComposerLockDiffDto::class, $composerLockDiffDto);
        $this->assertFalse($composerLockDiffDto->isEmpty());
        $this->assertIsArray($composerLockDiffDto->getRequireChanges());
        $this->assertIsArray($composerLockDiffDto->getRequireDevChanges());
    }

    /**
     * @return void
     */
    public function testRunUpToDate(): void
    {
        $this->markTestSkipped('TODO fix the test');

        // Arrange
        $processOutput = '{"changes":[],"changes-dev":[]}';
        $processRunnerMock = $this->mockProcessRunnerWithOutput($processOutput);
        $composerLockComparator = new ComposerLockComparator($processRunnerMock);
        $comparatorStep = new ComposerLockComparatorStep($composerLockComparator);

        // Act
        $stepsExecutionDto = $comparatorStep->run((new StepsResponseDto(true)));

        // Assert
        $this->assertFalse($stepsExecutionDto->getIsSuccessful());
        $this->assertEquals(
            'The branch is up to date. No further action is required.',
            $stepsExecutionDto->getOutputMessage(),
        );
        $this->assertNull($stepsExecutionDto->getPullRequestId());
    }

    /**
     * @return void
     */
    public function testRunProcessFailed(): void
    {
        $this->markTestSkipped('TODO fix the test');

        // Arrange
        $processRunnerMock = $this->mockProcessRunnerWithOutput('');
        $composerLockComparator = new ComposerLockComparator($processRunnerMock);
        $comparatorStep = new ComposerLockComparatorStep($composerLockComparator);

        // Act
        $stepsExecutionDto = $comparatorStep->run((new StepsResponseDto(true)));

        // Assert
        $this->assertFalse($stepsExecutionDto->getIsSuccessful());
        $this->assertEquals(
            'The branch is up to date. No further action is required.',
            $stepsExecutionDto->getOutputMessage(),
        );
        $this->assertNull($stepsExecutionDto->getPullRequestId());
    }

    /**
     * @param string $outputMessage
     *
     * @return \Core\Infrastructure\Service\ProcessRunnerService
     */
    protected function mockProcessRunnerWithOutput(string $outputMessage): ProcessRunnerService
    {
        $processMock = $this->createMock(Process::class);
        $processMock->method('getOutput')->willReturn($outputMessage);

        $processRunnerMock = $this->createMock(ProcessRunnerService::class);
        $processRunnerMock->method('run')->willReturn($processMock);

        return $processRunnerMock;
    }
}
