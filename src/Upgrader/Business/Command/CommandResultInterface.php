<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command;

interface CommandResultInterface
{
    /**
     * @return int
     */
    public function getResultCode(): int;

    /**
     * @return string
     */
    public function getResultString(): string;

    /**
     * @return bool
     */
    public function isSuccess(): bool;
}
