<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Response;

class UpgraderResponse implements UpgraderResponseInterface
{
    /**
     * @var int
     */
    protected $exitCode;

    /**
     * @var string|null
     */
    protected $output;

    /**
     * @param bool $isSuccessful
     * @param string|null $output
     */
    public function __construct(bool $isSuccessful, ?string $output = null)
    {
        $this->exitCode = $isSuccessful ? static::CODE_SUCCESS : static::CODE_ERROR;
        $this->output = $output;
    }

    /**
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    /**
     * @return string|null
     */
    public function getOutput(): ?string
    {
        return 'Upgrader: ' . $this->output;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->exitCode === static::CODE_SUCCESS;
    }
}
