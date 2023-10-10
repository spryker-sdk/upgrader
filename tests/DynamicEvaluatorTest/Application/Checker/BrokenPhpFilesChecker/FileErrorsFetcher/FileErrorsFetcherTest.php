<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher;

use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Baseline\BaselineStorage;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher\FileErrorsFetcher;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

class FileErrorsFetcherTest extends TestCase
{
    /**
     * @dataProvider toolInvalidReturnDataProvider
     *
     * @param array<mixed> $toolOutput
     *
     * @return void
     */
    public function testFetchProjectFileErrorsAndSaveInBaseLineShouldValidate(array $toolOutput): void
    {
        // Arrange
        $this->expectException(InvalidArgumentException::class);

        $fileErrorsFetcher = new FileErrorsFetcher('', '', '', $this->createProcessRunnerServiceMock($toolOutput), new BaselineStorage(), $this->createLoggerMock());

        // Act
        $fileErrorsFetcher->fetchProjectFileErrorsAndSaveInBaseLine();
    }

    /**
     * @return array<mixed>
     */
    public function toolInvalidReturnDataProvider(): array
    {
        return [
            [['file' => ['src/someClass.php' => ['messages' => [['line' => 1, 'message' => 'test message']]]]]],
            [['files' => ['src/someClass.php' => ['message' => [['line' => 1, 'message' => 'test message']]]]]],
            [['files' => ['src/someClass.php' => ['messages' => [['lin' => 1, 'message' => 'test message']]]]]],
            [['files' => ['src/someClass.php' => ['messages' => [['line' => 1, 'messag' => 'test message']]]]]],
        ];
    }

    /**
     * @return void
     */
    public function testFetchProjectFileErrorsAndSaveInBaseLineShouldReturnEmptyWhenThisErrorInBaseLineStorage(): void
    {
        // Arrange
        $toolOutput = ['files' => ['src/someClass.php' => ['messages' => [['line' => 1, 'message' => 'test message']]]]];

        $baseLineStorage = new BaselineStorage();
        $baseLineStorage->addFileError(new FileErrorDto('src/someClass.php', 1, 'test message'));

        $fileErrorsFetcher = new FileErrorsFetcher('', '', '', $this->createProcessRunnerServiceMock($toolOutput), $baseLineStorage, $this->createLoggerMock());

        // Act
        $fileErrors = $fileErrorsFetcher->fetchProjectFileErrorsAndSaveInBaseLine();

        // Assert
        $this->assertEmpty($fileErrors);
    }

    /**
     * @return void
     */
    public function testFetchProjectFileErrorsAndSaveInBaseLineShouldFetchFileErrors(): void
    {
        // Arrange
        $toolOutput = ['files' => ['src/someClass.php' => ['messages' => [['line' => 1, 'message' => 'test message']]]]];

        $baseLineStorage = new BaselineStorage();
        $fileErrorsFetcher = new FileErrorsFetcher('', '', '', $this->createProcessRunnerServiceMock($toolOutput), $baseLineStorage, $this->createLoggerMock());

        // Act
        $fileErrors = $fileErrorsFetcher->fetchProjectFileErrorsAndSaveInBaseLine();

        // Assert
        $this->assertCount(1, $fileErrors);
        $this->assertSame('src/someClass.php', $fileErrors[0]->getFilename());
        $this->assertSame(1, $fileErrors[0]->getLine());
        $this->assertSame('test message', $fileErrors[0]->getMessage());
    }

    /**
     * @return void
     */
    public function testResetShouldInvokeBaselineStorageResetting(): void
    {
        // Arrange
        $baseLineStorageMock = $this->createMock(BaselineStorage::class);
        $baseLineStorageMock->expects($this->once())->method('clear');

        $fileErrorsFetcher = new FileErrorsFetcher('', '', '', $this->createProcessRunnerServiceMock([]), $baseLineStorageMock, $this->createLoggerMock());

        // Act
        $fileErrorsFetcher->reset();
    }

    /**
     * @param array<mixed> $returnValue
     *
     * @return \Core\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    public function createProcessRunnerServiceMock(array $returnValue): ProcessRunnerServiceInterface
    {
        $process = $this->createMock(Process::class);
        $process->method('getOutput')->willReturn(json_encode($returnValue, \JSON_THROW_ON_ERROR));

        $processRunnerService = $this->createMock(ProcessRunnerServiceInterface::class);
        $processRunnerService->method('mustRun')->willReturn($process);
        $processRunnerService->method('run')->willReturn($process);

        return $processRunnerService;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function createLoggerMock(): LoggerInterface
    {
        return $this->createMock(LoggerInterface::class);
    }

    /**
     * @return void
     */
    public function testRunWithoutProjectConfig(): void
    {
        // Arrange
        $toolOutput = ['files' => []];
        $processMock = $this->createMock(Process::class);
        $processMock->method('getOutput')->willReturn(json_encode($toolOutput, \JSON_THROW_ON_ERROR));
        /** @var \Core\Infrastructure\Service\ProcessRunnerServiceInterface&\PHPUnit\Framework\MockObject\MockObject $processRunnerServiceMock */
        $processRunnerServiceMock = $this->createMock(ProcessRunnerServiceInterface::class);
        $processRunnerServiceMock
            ->method('run')
            ->with(['phpstan', 'analyse', '-c', 'internal', '--memory-limit', '-1', '--error-format', 'prettyJson'])
            ->willReturn($processMock);

        $fileErrorsFetcher = new FileErrorsFetcher('internal', 'project', 'phpstan', $processRunnerServiceMock, new BaselineStorage(), $this->createLoggerMock(), 'nonexist.neon');

        // Act
        $fileErrors = $fileErrorsFetcher->fetchProjectFileErrorsAndSaveInBaseLine();

        // Assert
        $this->assertEmpty($fileErrors);
    }

    /**
     * @return void
     */
    public function testRunWithProjectConfig(): void
    {
        // Arrange
        $toolOutput = ['files' => []];
        $processMock = $this->createMock(Process::class);
        $processMock->method('getOutput')->willReturn(json_encode($toolOutput, \JSON_THROW_ON_ERROR));
        /** @var \Core\Infrastructure\Service\ProcessRunnerServiceInterface&\PHPUnit\Framework\MockObject\MockObject $processRunnerServiceMock */
        $processRunnerServiceMock = $this->createMock(ProcessRunnerServiceInterface::class);
        $processRunnerServiceMock
            ->method('run')
            ->with(['phpstan', 'analyse', '-c', 'project', '--memory-limit', '-1', '--error-format', 'prettyJson'])
            ->willReturn($processMock);

        $fileErrorsFetcher = new FileErrorsFetcher('internal', 'project', 'phpstan', $processRunnerServiceMock, new BaselineStorage(), $this->createLoggerMock(), 'phpstan.neon');

        // Act
        $fileErrors = $fileErrorsFetcher->fetchProjectFileErrorsAndSaveInBaseLine();

        // Assert
        $this->assertEmpty($fileErrors);
    }

    /**
     * @return void
     */
    public function testRunProcessWithTimeout(): void
    {
        // Arrange
        /** @var \Core\Infrastructure\Service\ProcessRunnerServiceInterface&\PHPUnit\Framework\MockObject\MockObject $processRunnerServiceMock */
        $processRunnerServiceMock = $this->createMock(ProcessRunnerServiceInterface::class);
        /** @var \Psr\Log\LoggerInterface&\PHPUnit\Framework\MockObject\MockObject $loggerMock */
        $loggerMock = $this->createLoggerMock();
        $loggerMock->method('debug')->with('timeout');
        $processRunnerServiceMock
            ->method('run')
            ->willThrowException(new Exception('timeout'));

        $fileErrorsFetcher = new FileErrorsFetcher('', '', '', $processRunnerServiceMock, new BaselineStorage(), $loggerMock, 'phpstan.neon');

        // Act
        $fileErrors = $fileErrorsFetcher->fetchProjectFileErrorsAndSaveInBaseLine();

        // Assert
        $this->assertCount(1, $fileErrors);
        $this->assertInstanceOf(FileErrorDto::class, $fileErrors[0]);
    }
}
