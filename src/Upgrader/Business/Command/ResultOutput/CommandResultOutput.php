<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command\ResultOutput;

class CommandResultOutput
{
    public const SUCCESS_STATUS_CODE = 0;
    public const ERROR_STATUS_CODE = 1;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string|null
     */
    protected $command;

    /**
     * @param int $statusCode
     * @param string $message
     * @param string|null $command
     */
    public function __construct(int $statusCode, string $message, ?string $command = null)
    {
        $this->statusCode = $statusCode;
        $this->message = $message;
        $this->command = $command;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        if ($this->command) {
            return $this->command . PHP_EOL . $this->message;
        }

        return $this->message;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->statusCode === static::SUCCESS_STATUS_CODE;
    }
}
