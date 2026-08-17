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
    protected PackagesDirProviderInterface $packagesDirProvider;

    protected ProcessRunnerServiceInterface $processRunner;

    protected PublicApiFilePathsProviderInterface $publicApiFilePathsProvider;

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
