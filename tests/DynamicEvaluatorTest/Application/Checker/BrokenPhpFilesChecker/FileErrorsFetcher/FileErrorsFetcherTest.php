<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher;

use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Baseline\BaselineStorage;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher\FileErrorsFetcher;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
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

        $fileErrorsFetcher = new FileErrorsFetcher('', '', $this->createProcessRunnerServiceMock($toolOutput), new BaselineStorage(), $this->createLoggerMock());

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

        $fileErrorsFetcher = new FileErrorsFetcher('', '', $this->createProcessRunnerServiceMock($toolOutput), $baseLineStorage, $this->createLoggerMock());

        // Act
        $fileErrors = $fileErrorsFetcher->fetchProjectFileErrorsAndSaveInBaseLine();

        // Assert
        $this->assertEmpty($fileErrors);
    }

    /**
     * @dataProvider projectFileErrorsDataProvider
     *
     * @param array<mixed> $toolOutput
     * @param array<mixed> $expectedResult
     *
     * @return void
     */
    public function testFetchProjectFileErrorsAndSaveInBaseLineShouldFetchFileErrors(array $toolOutput, array $expectedResult): void
    {
        // Arrange
        $baseLineStorage = new BaselineStorage();
        $fileErrorsFetcher = new FileErrorsFetcher('', '', $this->createProcessRunnerServiceMock($toolOutput), $baseLineStorage, $this->createLoggerMock());

        // Act
        $fileErrors = $fileErrorsFetcher->fetchProjectFileErrorsAndSaveInBaseLine();

        // Assert
        $this->assertCount(1, $fileErrors);
        $this->assertSame($expectedResult['expectedClass'], $fileErrors[0]->getFilename());
        $this->assertSame($expectedResult['expectedLine'], $fileErrors[0]->getLine());
        $this->assertSame($expectedResult['expectedMessage'], $fileErrors[0]->getMessage());
    }

    /**
     * @return array<mixed>
     */
    public function projectFileErrorsDataProvider(): array
    {
        return [
            [
                ['files' => ['src/someClass.php' => ['messages' => [['line' => 1, 'message' => 'test message']]]]],
                ['expectedClass' => 'src/someClass.php', 'expectedLine' => 1, 'expectedMessage' => 'test message'],
            ],
            [
                ['files' => ['src/someClass.php' => ['messages' => [['line' => null, 'message' => 'test message']]]]],
                ['expectedClass' => 'src/someClass.php', 'expectedLine' => 0, 'expectedMessage' => 'test message'],
            ],
            [
                ['files' => ['src/someClass.php' => ['messages' => [['line' => 1, 'message' => null]]]]],
                ['expectedClass' => 'src/someClass.php', 'expectedLine' => 1, 'expectedMessage' => ''],
            ],
        ];
    }

    /**
     * @return void
     */
    public function testResetShouldInvokeBaselineStorageResetting(): void
    {
        // Arrange
        $baseLineStorageMock = $this->createMock(BaselineStorage::class);
        $baseLineStorageMock->expects($this->once())->method('clear');

        $fileErrorsFetcher = new FileErrorsFetcher('', '', $this->createProcessRunnerServiceMock([]), $baseLineStorageMock, $this->createLoggerMock());

        // Act
        $fileErrorsFetcher->reset();
    }

    /**
     * @param array<mixed> $returnValue
     *
     * @return \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface
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
        /** @var \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface&\PHPUnit\Framework\MockObject\MockObject $processRunnerServiceMock */
        $processRunnerServiceMock = $this->createMock(ProcessRunnerServiceInterface::class);
        $processRunnerServiceMock
            ->method('run')
            ->with(['phpstan', 'analyse', '-c', 'internal', '--error-format', 'prettyJson'])
            ->willReturn($processMock);

        $fileErrorsFetcher = new FileErrorsFetcher('internal', 'phpstan', $processRunnerServiceMock, new BaselineStorage(), $this->createLoggerMock(), 'nonexist.neon');

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
        /** @var \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface&\PHPUnit\Framework\MockObject\MockObject $processRunnerServiceMock */
        $processRunnerServiceMock = $this->createMock(ProcessRunnerServiceInterface::class);
        /** @var \Psr\Log\LoggerInterface&\PHPUnit\Framework\MockObject\MockObject $loggerMock */
        $loggerMock = $this->createLoggerMock();
        $loggerMock->method('debug')->with('The process "test_process" exceeded the timeout of 900 seconds.');
        $processMock = $this->createMock(Process::class);
        $processMock->method('getCommandLine')->willReturn('test_process');
        $processMock->method('getTimeout')->willReturn(ProcessRunnerServiceInterface::DEFAULT_PROCESS_TIMEOUT);
        $processRunnerServiceMock
            ->method('run')
            ->willThrowException(new ProcessTimedOutException($processMock, ProcessTimedOutException::TYPE_GENERAL));

        $fileErrorsFetcher = new FileErrorsFetcher('', '', $processRunnerServiceMock, new BaselineStorage(), $loggerMock, 'phpstan.neon');

        // Act
        $fileErrors = $fileErrorsFetcher->fetchProjectFileErrorsAndSaveInBaseLine();

        // Assert
        $this->assertCount(1, $fileErrors);
        $this->assertInstanceOf(FileErrorDto::class, $fileErrors[0]);
        $this->assertSame(sprintf(
            'Cannot detect broken PHP files because PHPStan fails with an error “Timeout %s”. To check manually, run `vendor/bin/phpstan analyse src/` from project root dir',
            ProcessRunnerServiceInterface::DEFAULT_PROCESS_TIMEOUT,
        ), $fileErrors[0]->getMessage());
    }

    /**
     * @return void
     */
    public function testRunProcessWithException(): void
    {
        // Arrange
        /** @var \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface&\PHPUnit\Framework\MockObject\MockObject $processRunnerServiceMock */
        $processRunnerServiceMock = $this->createMock(ProcessRunnerServiceInterface::class);
        /** @var \Psr\Log\LoggerInterface&\PHPUnit\Framework\MockObject\MockObject $loggerMock */
        $loggerMock = $this->createLoggerMock();
        $loggerMock->method('debug')->with('error');
        $processRunnerServiceMock
            ->method('run')
            ->willThrowException(new Exception('error'));

        $fileErrorsFetcher = new FileErrorsFetcher('', '', $processRunnerServiceMock, new BaselineStorage(), $loggerMock, 'phpstan.neon');

        // Act
        $fileErrors = $fileErrorsFetcher->fetchProjectFileErrorsAndSaveInBaseLine();

        // Assert
        $this->assertCount(1, $fileErrors);
        $this->assertInstanceOf(FileErrorDto::class, $fileErrors[0]);
        $this->assertSame(sprintf(
            'Cannot detect broken PHP files because PHPStan fails. To check manually, run `vendor/bin/phpstan analyse src/` from project root dir',
            ProcessRunnerServiceInterface::DEFAULT_PROCESS_TIMEOUT,
        ), $fileErrors[0]->getMessage());
    }

    /**
     * @return void
     */
    public function testFetchProjectFileErrorsExecutesPerDir(): void
    {
        // Arrange
        $returnValues = [
            json_encode(['files' => ['src/someClass.php' => ['messages' => [['line' => 1, 'message' => 'test message']]]]], \JSON_THROW_ON_ERROR),
            json_encode(['files' => ['src/someClass2.php' => ['messages' => [['line' => 1, 'message' => 'test message2']]]]], \JSON_THROW_ON_ERROR),
        ];

        $process = $this->createMock(Process::class);
        $process->expects($this->exactly(2))
            ->method('getOutput')
            ->willReturnOnConsecutiveCalls(...$returnValues);

        /** @var \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface&\PHPUnit\Framework\MockObject\MockObject $processRunnerServiceMock */
        $processRunnerServiceMock = $this->createMock(ProcessRunnerServiceInterface::class);
        $processRunnerServiceMock->expects($this->exactly(2))
            ->method('run')
            ->willReturn($process);

        $fileErrorsFetcher = new FileErrorsFetcher(
            'config/DynamicEvaluator/checker_phpstan.neon',
            '',
            $processRunnerServiceMock,
            new BaselineStorage(),
            $this->createLoggerMock(),
        );

        // Act
        $fileErrors = $fileErrorsFetcher->fetchProjectFileErrorsAndSaveInBaseLine(['dir1', 'dir2']);

        // Assert
        $this->assertCount(2, $fileErrors);
        $this->assertSame('src/someClass.php', $fileErrors[0]->getFilename());
        $this->assertSame(1, $fileErrors[0]->getLine());
        $this->assertSame('test message', $fileErrors[0]->getMessage());
        $this->assertSame('src/someClass2.php', $fileErrors[1]->getFilename());
        $this->assertSame(1, $fileErrors[1]->getLine());
        $this->assertSame('test message2', $fileErrors[1]->getMessage());
    }
}
