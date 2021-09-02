<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

class UpgraderResult implements UpgraderResultInterface
{
    /**
     * @var bool
     */
    protected $success;

    /**
     * @var string|null
     */
    protected $message;

    /**
     * @param bool $success
     * @param string|null $message
     */
    public function __construct(bool $success, ?string $message = null)
    {
        $this->success = $success;
        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param bool $success
     *
     * @return void
     */
    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    /**
     * @param string|null $message
     *
     * @return void
     */
    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }
}
