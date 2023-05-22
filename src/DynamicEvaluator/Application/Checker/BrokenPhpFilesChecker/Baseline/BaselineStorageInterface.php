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
 /**
  * @param \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto $fileErrorDto
  *
  * @return void
  */
    public function addFileError(FileErrorDto $fileErrorDto): void;

    /**
     * @param \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto $fileErrorDto
     *
     * @return bool
     */
    public function hasFileError(FileErrorDto $fileErrorDto): bool;

    /**
     * @return void
     */
    public function clear(): void;
}
