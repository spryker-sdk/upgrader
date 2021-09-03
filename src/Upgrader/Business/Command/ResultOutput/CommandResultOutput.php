<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command\ResultOutput;

class CommandResultOutput
{
    protected const SUCCESS_STATUS_CODE = 0;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $message;

    /**
     * @param int $statusCode
     * @param string $message
     */
    public function __construct(int $statusCode, string $message)
    {
        $this->statusCode = $statusCode;
        $this->message = $message;
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
