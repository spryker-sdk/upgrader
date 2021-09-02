<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command;

class CommandResult implements CommandResultInterface
{
    /**
     * @var int
     */
    protected $resultCode;

    /**
     * @var string
     */
    protected $resultString;

    /**
     * @param int $resultCode
     * @param string $resultString
     */
    public function __construct(int $resultCode, string $resultString)
    {
        $this->resultCode = $resultCode;
        $this->resultString = $resultString;
    }

    /**
     * @return int
     */
    public function getResultCode(): int
    {
        return $this->resultCode;
    }

    /**
     * @return string
     */
    public function getResultString(): string
    {
        return $this->resultString;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return !$this->resultCode;
    }
}
