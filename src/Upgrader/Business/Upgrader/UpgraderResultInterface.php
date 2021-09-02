<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

interface UpgraderResultInterface
{
    /**
     * @return bool
     */
    public function isSuccess(): bool;

    /**
     * @return string|null
     */
    public function getMessage(): ?string;

    /**
     * @param bool $success
     *
     * @return void
     */
    public function setSuccess(bool $success): void;

    /**
     * @param string|null $message
     *
     * @return void
     */
    public function setMessage(?string $message): void;
}
