<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Baseline;

use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto;

interface BaselineStorageInterface
{
    public function addFileError(FileErrorDto $fileErrorDto): void;

    public function hasFileError(FileErrorDto $fileErrorDto): bool;

    public function clear(): void;
}
