<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Baseline;

use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto;

class BaselineStorage implements BaselineStorageInterface
{
    /**
     * @var array<string, array<string>>
     */
    protected array $fileErrors;

    /**
     * @param \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto $fileErrorDto
     *
     * @return void
     */
    public function addFileError(FileErrorDto $fileErrorDto): void
    {
        if ($this->hasFileError($fileErrorDto)) {
            return;
        }

        if (!isset($this->fileErrors[$fileErrorDto->getFilename()])) {
            $this->fileErrors[$fileErrorDto->getFilename()] = [];
        }

        $this->fileErrors[$fileErrorDto->getFilename()][] = $fileErrorDto->getMessage();
    }

    /**
     * @param \DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto\FileErrorDto $fileErrorDto
     *
     * @return bool
     */
    public function hasFileError(FileErrorDto $fileErrorDto): bool
    {
        return isset($this->fileErrors[$fileErrorDto->getFilename()])
            && in_array($fileErrorDto->getMessage(), $this->fileErrors[$fileErrorDto->getFilename()], true);
    }

    /**
     * @return array<string, array<string>>
     */
    public function getFileErrors(): array
    {
        return $this->fileErrors;
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->fileErrors = [];
    }
}
