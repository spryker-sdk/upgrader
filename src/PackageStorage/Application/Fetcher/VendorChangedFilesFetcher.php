<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace PackageStorage\Application\Fetcher;

use PackageStorage\Application\PackagesSynchronizer\PackagesDirProviderInterface;
use PackageStorage\Application\PublicApiFilePathsProvider\PublicApiFilePathsProviderInterface;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;

class VendorChangedFilesFetcher implements VendorChangedFilesFetcherInterface
{
    /**
     * @var \PackageStorage\Application\PackagesSynchronizer\PackagesDirProviderInterface
     */
    protected PackagesDirProviderInterface $packagesDirProvider;

    /**
     * @var \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunner;

    /**
     * @var \PackageStorage\Application\PublicApiFilePathsProvider\PublicApiFilePathsProviderInterface
     */
    protected PublicApiFilePathsProviderInterface $publicApiFilePathsProvider;

    /**
     * @param \PackageStorage\Application\PackagesSynchronizer\PackagesDirProviderInterface $packagesDirProvider
     * @param \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface $processRunner
     * @param \PackageStorage\Application\PublicApiFilePathsProvider\PublicApiFilePathsProviderInterface $publicApiFilePathsProvider
     */
    public function __construct(
        PackagesDirProviderInterface $packagesDirProvider,
        ProcessRunnerServiceInterface $processRunner,
        PublicApiFilePathsProviderInterface $publicApiFilePathsProvider
    ) {
        $this->packagesDirProvider = $packagesDirProvider;
        $this->processRunner = $processRunner;
        $this->publicApiFilePathsProvider = $publicApiFilePathsProvider;
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
                (grep -E -v '.*/(tests|test)/.*' || true) | \
                {EXCLUDE_PUBLIC_API_FILES}
                COMMAND,
            [
                '{TO_PATH_ESC}' => escapeshellarg($toDir),
                '{FROM_PATH_ESC}' => escapeshellarg($fromDir),
                '{TO_PATH}' => $toDir,
                '{FROM_PATH}' => $fromDir,
                '{TO_PATH_SED}' => str_replace('/', '\/', $toDir),
                '{EXCLUDE_PUBLIC_API_FILES}' => $this->getExcludedPublicApiFiles(),
            ],
        );
    }

    /**
     * @return string
     */
    protected function getExcludedPublicApiFiles(): string
    {
        return implode(
            ' | ',
            array_map(
                static fn (string $el): string => sprintf('(grep -E -v \'%s\' || true)', $el),
                $this->publicApiFilePathsProvider->getPublicApiFilePathsRegexCollection(),
            ),
        );
    }
}
