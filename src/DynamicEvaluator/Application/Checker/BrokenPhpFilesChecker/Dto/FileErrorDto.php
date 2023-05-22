<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\Dto;

class FileErrorDto
{
    /**
     * @var string
     */
    protected string $filename;

    /**
     * @var int
     */
    protected int $line;

    /**
     * @var string
     */
    protected string $message;

    /**
     * @param string $filename
     * @param int $line
     * @param string $message
     */
    public function __construct(string $filename, int $line, string $message)
    {
        $this->filename = $filename;
        $this->line = $line;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
