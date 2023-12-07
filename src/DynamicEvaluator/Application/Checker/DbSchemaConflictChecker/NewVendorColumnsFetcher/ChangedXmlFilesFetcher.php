<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\NewVendorColumnsFetcher;

use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;

class ChangedXmlFilesFetcher
{
    /**
     * @var \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunner;

    /**
     * @param \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface $processRunner
     */
    public function __construct(ProcessRunnerServiceInterface $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @param string $fromDir
     * @param string $toDir
     *
     * @return array<string>
     */
    public function fetchChangedXmlSchemaFiles(string $fromDir, string $toDir): array
    {
        $process = $this->processRunner->mustRunFromCommandLine($this->getCommand($fromDir, $toDir));

        $output = trim($process->getOutput());

        if ($output === '') {
            return [];
        }

        return explode(PHP_EOL, $output);
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
                diff -qNr {TO_PATH_ESC} {FROM_PATH_ESC} | \
                grep 'schema.xml' | grep 'vendor/spryker' | \
                (grep 'Only in {TO_PATH}:\|Files {TO_PATH}' || true) | \
                sed -E 's/^Only in {TO_PATH_SED}: //' | \
                sed -E 's/Files {TO_PATH_SED}(\S+)(.*)/\1/'
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
