<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker;

use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\ViolationDto;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher\FileErrorsFetcherInterface;

class BrokenPhpFilesChecker
{
    /**
     * @var \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher\FileErrorsFetcherInterface
     */
    protected FileErrorsFetcherInterface $fileErrorsFetcher;

    /**
     * @param \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher\FileErrorsFetcherInterface $fileErrorsFetcher
     */
    public function __construct(FileErrorsFetcherInterface $fileErrorsFetcher)
    {
        $this->fileErrorsFetcher = $fileErrorsFetcher;
    }

    /**
     * @param array<string> $composerCommands
     *
     * @return array<\DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\ViolationDto>
     */
    public function check(array $composerCommands): array
    {
        $fileErrors = $this->fileErrorsFetcher->fetchNewProjectFileErrors();

        if (count($fileErrors) === 0) {
            return [];
        }

        return [new ViolationDto($composerCommands, $fileErrors)];
    }
}
