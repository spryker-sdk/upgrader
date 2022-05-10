<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Dto;


class ExecutionDto
{
    /**
     * @var bool
     */
    protected bool $isSuccessful;

    /**
     * @var string|null
     */
    protected ?string $outputMessage;

    /**
     * @param bool $isSuccessful
     * @param string|null $outputMessage
     */
    public function __construct(bool $isSuccessful, ?string $outputMessage = null)
    {
        $this->isSuccessful = $isSuccessful;
        $this->outputMessage = $outputMessage;
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * @return string|null
     */
    public function getOutputMessage(): ?string
    {
        return $this->outputMessage;
    }
}
