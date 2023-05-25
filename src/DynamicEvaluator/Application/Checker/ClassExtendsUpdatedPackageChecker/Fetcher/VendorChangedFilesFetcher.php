<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher;

use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesDirProviderInterface;

class VendorChangedFilesFetcher implements VendorChangedFilesFetcherInterface
{
    /**
     * @var \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesDirProviderInterface
     */
    protected PackagesDirProviderInterface $packagesDirProvider;

    /**
     * @var \Core\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunner;

    /**
     * @param \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesDirProviderInterface $packagesDirProvider
     * @param \Core\Infrastructure\Service\ProcessRunnerServiceInterface $processRunner
     */
    public function __construct(PackagesDirProviderInterface $packagesDirProvider, ProcessRunnerServiceInterface $processRunner)
    {
        $this->packagesDirProvider = $packagesDirProvider;
        $this->processRunner = $processRunner;
    }

    /**
     * @return array<string> List of diff files
     */
    public function fetchChangedFiles(): array
    {
        $files = [];

        foreach ($this->packagesDirProvider->getSprykerPackageDirs() as $dir) {
            $files[] = $this->executeCommandForDir($dir);
        }

        return array_merge(...$files);
    }

    /**
     * @param string $dir
     *
     * @return array<string>
     */
    protected function executeCommandForDir(string $dir): array
    {
        $fromDir = $this->packagesDirProvider->getFromDir() . $dir;
        $toDir = $this->packagesDirProvider->getToDir() . $dir;

        $process = $this->processRunner->mustRunFromCommandLine($this->getCommand($fromDir, $toDir));

        $output = trim($process->getOutput());

        if ($output === '') {
            return [];
        }

        return array_map(
            static fn (string $relativePath): string => $toDir . DIRECTORY_SEPARATOR . ltrim($relativePath, DIRECTORY_SEPARATOR),
            explode(PHP_EOL, $output),
        );
    }

    /**
     * @param string $fromDir
     * @param string $toDir
     *
     * @return string
     */
    protected function getCommand(string $fromDir, string $toDir): string
    {
        return strtr(
            <<<'COMMAND'
                diff -qr {TO_PATH_ESC} {FROM_PATH_ESC} | \
                (grep 'Only in {TO_PATH}:\|Files {TO_PATH}' || true) | \
                sed -E 's/^Only in {TO_PATH_SED}: //' | \
                sed -E 's/Files {TO_PATH_SED}(\S+)(.*)/\1/' | \
                (grep -E '\.php$' || true) | \
                (grep -E -v '(Test|Interface|Trait)\.php$' || true) | \
                (grep -E -v '.*/(tests|test)/.*' || true)
                COMMAND,
            [
                '{TO_PATH_ESC}' => escapeshellarg($toDir),
                '{FROM_PATH_ESC}' => escapeshellarg($fromDir),
                '{TO_PATH}' => $toDir,
                '{FROM_PATH}' => $fromDir,
                '{TO_PATH_SED}' => str_replace('/', '\/', $toDir),
            ],
        );
    }
}
