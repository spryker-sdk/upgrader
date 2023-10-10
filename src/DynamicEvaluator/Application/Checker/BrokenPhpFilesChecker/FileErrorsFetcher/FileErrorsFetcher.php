<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher;

use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Baseline\BaselineStorageInterface;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use RuntimeException;

class FileErrorsFetcher implements FileErrorsFetcherInterface
{
    /**
     * @var string
     */
    protected string $executableConfig;

    /**
     * @var string
     */
    protected string $executableProjectConfig;

    /**
     * @var string
     */
    protected string $executable;

    /**
     * @var \Core\Infrastructure\Service\ProcessRunnerServiceInterface
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
     * @param string $executableProjectConfig
     * @param string $executable
     * @param \Core\Infrastructure\Service\ProcessRunnerServiceInterface $processRunnerService
     * @param \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Baseline\BaselineStorageInterface $baselineStorage
     * @param \Psr\Log\LoggerInterface $logger
     * @param string $phpstanNeonFileName
     */
    public function __construct(
        string $executableConfig,
        string $executableProjectConfig,
        string $executable,
        ProcessRunnerServiceInterface $processRunnerService,
        BaselineStorageInterface $baselineStorage,
        LoggerInterface $logger,
        string $phpstanNeonFileName = 'phpstan.neon'
    ) {
        $this->executableConfig = $executableConfig;
        $this->executableProjectConfig = $executableProjectConfig;
        $this->executable = $executable;
        $this->processRunnerService = $processRunnerService;
        $this->baselineStorage = $baselineStorage;
        $this->logger = $logger;
        $this->phpstanNeonFileName = $phpstanNeonFileName;
    }

    /**
     * @return array<\DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto>
     */
    public function fetchProjectFileErrorsAndSaveInBaseLine(): array
    {
        $fileErrors = [];

        try {
            $errors = $this->fetchErrorsArray();
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());

            return [
                new FileErrorDto('src', 0, 'Unable to identify corrupted files; kindly execute phpstan manually'),
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

        $fileError = new FileErrorDto($file, $message['line'], $message['message']);

        if ($this->baselineStorage->hasFileError($fileError)) {
            return null;
        }

        $this->baselineStorage->addFileError($fileError);

        return new FileErrorDto($file, $message['line'], $message['message']);
    }

    /**
     * @throws \RuntimeException
     *
     * @return array<mixed>
     */
    protected function fetchErrorsArray(): array
    {
        $process = $this->processRunnerService->run([
            $this->executable,
            'analyse',
            '-c',
            file_exists(getcwd() . DIRECTORY_SEPARATOR . $this->phpstanNeonFileName) ? $this->executableProjectConfig : $this->executableConfig,
            '--memory-limit',
            '-1',
            '--error-format',
            'prettyJson',
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
        if (!isset($data[$key]) || ($isArray && !is_array($data[$key]))) {
            throw new InvalidArgumentException(sprintf(
                'Unable to find %s key or it\'s not an array in %s. Tooling export format is changes.',
                $key,
                substr(json_encode($data, \JSON_THROW_ON_ERROR), 0, 100),
            ));
        }
    }
}
