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
     * @param string $executableConfig
     * @param string $executable
     * @param \Core\Infrastructure\Service\ProcessRunnerServiceInterface $processRunnerService
     * @param \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Baseline\BaselineStorageInterface $baselineStorage
     */
    public function __construct(
        string $executableConfig,
        string $executable,
        ProcessRunnerServiceInterface $processRunnerService,
        BaselineStorageInterface $baselineStorage
    ) {
        $this->executableConfig = $executableConfig;
        $this->executable = $executable;
        $this->processRunnerService = $processRunnerService;
        $this->baselineStorage = $baselineStorage;
    }

    /**
     * @return array<\DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto>
     */
    public function fetchProjectFileErrorsAndSaveInBaseLine(): array
    {
        $fileErrors = [];

        $errors = $this->fetchErrorsArray();

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
            $this->executableConfig,
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
