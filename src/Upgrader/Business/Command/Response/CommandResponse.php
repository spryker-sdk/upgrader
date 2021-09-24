<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command\Response;

class CommandResponse
{
    public const CODE_ERROR = 1;
    public const CODE_SUCCESS = 0;

    /**
     * @var int
     */
    protected $exitCode;

    /**
     * @var string|null
     */
    protected $processCommand;

    /**
     * @var string|null
     */
    protected $commandName;

    /**
     * @var string|null
     */
    protected $output;

    /**
     * @param bool $isSuccessful
     * @param string|null $output
     * @param string|null $commandName
     */
    public function __construct(bool $isSuccessful, ?string $output = null, ?string $commandName = null)
    {
        $this->exitCode = $isSuccessful ? self::CODE_SUCCESS : self::CODE_ERROR;
        $this->output = $output;
        $this->commandName = $commandName;
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
    public function getProcessCommand(): ?string
    {
        return $this->processCommand;
    }

    /**
     * @return string|null
     */
    public function getCommandName(): ?string
    {
        return $this->commandName;
    }

    /**
     * @return string|null
     */
    public function getOutput(): ?string
    {
        return $this->output;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->exitCode === static::CODE_SUCCESS;
    }
}
