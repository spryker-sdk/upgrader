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
use Nette\Neon\Neon;
use Psr\Log\LoggerInterface;
use RuntimeException;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

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

        if ($dirs === []) {
            $dirs = $this->getDirectories($dirs);
        }

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

    /**
     * @param array<string> $dirs
     *
     * @return array<mixed>
     */
    protected function fetchErrorsArray(array $dirs): array
    {
        if ($dirs !== []) {
            return $this->fetchErrorsArrayPerDirectory($dirs);
        }

        $process = $this->processRunnerService->run([
            $this->executable,
            'analyse',
            '-c',
            $this->executableConfig,
            '--error-format',
            'prettyJson',
            ...$dirs,
        ]);

        return $this->runProcess($process);
    }

    /**
     * @param array<string> $dirs
     *
     * @return array<string, mixed>
     */
    protected function fetchErrorsArrayPerDirectory(array $dirs): array
    {
        $result = [];

        foreach ($dirs as $dir) {
            $process = $this->processRunnerService->run([
                $this->executable,
                'analyse',
                '-c',
                $this->executableConfig,
                '--error-format',
                'prettyJson',
                $dir,
            ]);

            $result = array_merge_recursive($result, $this->runProcess($process));
        }

        return $result;
    }

    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @throws \RuntimeException
     *
     * @return array<string, mixed>
     */
    protected function runProcess(Process $process): array
    {
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
     * @param string $neonFilePath
     *
     * @return array<string, mixed>
     */
    protected function parseNeonFile(string $neonFilePath): array
    {
        if (file_exists($neonFilePath) === false) {
            return [];
        }

        $neonContent = file_get_contents($neonFilePath);

        if ($neonContent === false) {
            return [];
        }

        return Neon::decode($neonContent);
    }

    /**
     * @param array<string> $dirs
     *
     * @return array<string>
     */
    protected function getDirectories(array $dirs = []): array
    {
        $config = $this->parseNeonFile($this->executableConfig);

        if ($config === []) {
            return $dirs;
        }

        foreach ($config['parameters']['paths'] as $basePath) {
            $basePath = str_replace('%currentWorkingDirectory%', getcwd() ?: '', $basePath);
            $dirs = array_merge($dirs, $this->findDirectories($basePath, $config['parameters']['excludePaths']['analyse']));
        }

        return $dirs;
    }

    /**
     * @param string $baseDir
     * @param array<string> $excludePaths
     *
     * @return array<string>
     */
    protected function findDirectories(string $baseDir, array $excludePaths): array
    {
        $finder = new Finder();
        $finder->directories()->in($baseDir)->depth('== 1');

        foreach ($excludePaths as $excludePath) {
            $finder->notPath($excludePath);
        }

        $dirs = [];
        foreach ($finder as $dir) {
            $dirs[] = $dir->getRealPath();
        }

        return $dirs;
    }
}
