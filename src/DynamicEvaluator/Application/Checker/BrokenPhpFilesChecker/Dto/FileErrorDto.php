<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto;

class FileErrorDto
{
    protected string $filename;

    protected int $line;

    protected string $message;

    public function __construct(string $filename, int $line, string $message)
    {
        $this->filename = $filename;
        $this->line = $line;
        $this->message = $message;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
