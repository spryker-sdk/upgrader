<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Dto;

class ResponseDto
{
    protected bool $isSuccessful;

    protected ?string $outputMessage;

    public function __construct(bool $isSuccessful, ?string $outputMessage = null)
    {
        $this->isSuccessful = $isSuccessful;
        $this->outputMessage = $outputMessage;
    }

    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    public function getOutputMessage(): ?string
    {
        return $this->outputMessage;
    }
}
