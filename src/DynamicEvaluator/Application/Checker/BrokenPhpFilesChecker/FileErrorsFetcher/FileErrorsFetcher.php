<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher;

use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Baseline\BaselineStorageInterface;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Exception\ProcessTimedOutException;

class FileErrorsFetcher implements FileErrorsFetcherInterface
{
    /**
     * @var string
     */
    protected string $executableConfig;

    /**
     * @var string
     */
    protected string $executable;

    /**
     * @var \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunnerService;

    /**
     * @var \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Baseline\BaselineStorageInterface
     */
    protected BaselineStorageInterface $baselineStorage;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var string
     */
    protected string $phpstanNeonFileName;

    /**
     * @param string $executableConfig
     * @param string $executable
     * @param \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface $processRunnerService
     * @param \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Baseline\BaselineStorageInterface $baselineStorage
     * @param \Psr\Log\LoggerInterface $logger
     * @param string $phpstanNeonFileName
     */
    public function __construct(
        string $executableConfig,
        string $executable,
        ProcessRunnerServiceInterface $processRunnerService,
        BaselineStorageInterface $baselineStorage,
        LoggerInterface $logger,
        string $phpstanNeonFileName = 'phpstan.neon'
    ) {
        $this->executableConfig = $executableConfig;
        $this->executable = $executable;
        $this->processRunnerService = $processRunnerService;
        $this->baselineStorage = $baselineStorage;
        $this->logger = $logger;
        $this->phpstanNeonFileName = $phpstanNeonFileName;
    }

    /**
     * @param array<string> $dirs
     *
     * @return array<\DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto>
     */
    public function fetchProjectFileErrorsAndSaveInBaseLine(array $dirs = []): array
    {
        $fileErrors = [];

        try {
            $errors = $this->fetchErrorsArray($dirs);
        } catch (ProcessTimedOutException $e) {
            $this->logger->warning($e->getMessage());

            return [
                new FileErrorDto(
                    'src',
                    0,
                    sprintf(
                        'Cannot detect broken PHP files because PHPStan fails with an error “Timeout %s”. To check manually, run `vendor/bin/phpstan analyse src/` from project root dir',
                        ProcessRunnerServiceInterface::DEFAULT_PROCESS_TIMEOUT,
                    ),
                ),
            ];
        } catch (Exception $e) {
            $this->logger->warning($e->getMessage());

            return [
                new FileErrorDto(
                    'src',
                    0,
                    'Cannot detect broken PHP files because PHPStan fails. To check manually, run `vendor/bin/phpstan analyse src/` from project root dir',
                ),
            ];
        }

        $this->assertArrayKeyExists($errors, 'files', true);

        foreach ($errors['files'] as $file => $data) {
            $this->assertArrayKeyExists($data, 'messages', true);

            foreach ($data['messages'] as $message) {
                $newFileError = $this->fetchNewFileError($file, $message);

                if ($newFileError !== null) {
                    $fileErrors[] = $newFileError;
                }
            }
        }

        return $fileErrors;
    }

    /**
     * @param string $file
     * @param array<mixed> $message
     *
     * @return \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto|null
     */
    protected function fetchNewFileError(string $file, array $message): ?FileErrorDto
    {
        $this->assertArrayKeyExists($message, 'line');
        $this->assertArrayKeyExists($message, 'message');

        $fileError = new FileErrorDto($file, (int)$message['line'], (string)$message['message']);

        if ($this->baselineStorage->hasFileError($fileError)) {
            return null;
        }

        $this->baselineStorage->addFileError($fileError);

        return new FileErrorDto($file, (int)$message['line'], (string)$message['message']);
    }

    /**
     * @param array<string> $dirs
     *
     * @throws \RuntimeException
     *
     * @return array<mixed>
     */
    protected function fetchErrorsArray(array $dirs): array
    {
        $process = $this->processRunnerService->run([
            $this->executable,
            'analyse',
            '-c',
            $this->executableConfig,
            '--error-format',
            'prettyJson',
            ...$dirs,
        ]);

        try {
            $result = json_decode($process->getOutput(), true, 512, \JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new RuntimeException(
                sprintf(
                    'Command: %s. Error: %s. Output: %s. Err: %s, Code: %s',
                    $process->getCommandLine(),
                    $e->getMessage(),
                    $process->getOutput(),
                    $process->getErrorOutput(),
                    $process->getExitCode(),
                ),
            );
        }

        return $result;
    }

    /**
     * @return void
     */
    public function reset(): void
    {
        $this->processRunnerService->mustRun([
            $this->executable,
            'clear-result-cache',
            '-c',
            $this->executableConfig,
        ]);

        $this->baselineStorage->clear();
    }

    /**
     * @param array<mixed> $data
     * @param string $key
     * @param bool $isArray
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function assertArrayKeyExists(array $data, string $key, bool $isArray = false): void
    {
        if (!array_key_exists($key, $data) || ($isArray && !is_array($data[$key]))) {
            throw new InvalidArgumentException(sprintf(
                'Unable to find %s key or it\'s not an array in %s. Tooling export format is changes.',
                $key,
                substr(json_encode($data, \JSON_THROW_ON_ERROR), 0, 100),
            ));
        }
    }
}
