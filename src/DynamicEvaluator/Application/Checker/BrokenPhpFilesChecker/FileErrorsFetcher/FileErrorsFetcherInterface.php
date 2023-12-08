<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\FileErrorsFetcher;

interface FileErrorsFetcherInterface
{
    /**
     * @param array<string> $dirs
     *
     * @return array<\DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto>
     */
    public function fetchProjectFileErrorsAndSaveInBaseLine(array $dirs = []): array;

    /**
     * @return void
     */
    public function reset(): void;
}
