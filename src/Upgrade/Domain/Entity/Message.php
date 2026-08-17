<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Domain\Entity;

use SprykerSdk\SdkContracts\Entity\MessageInterface;

class Message implements MessageInterface
{
    protected string $message = '';

    protected int $verbosity = 1;

    public function __construct(string $message = '', int $verbosity = 1)
    {
        $this->message = $message;
        $this->verbosity = $verbosity;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getVerbosity(): int
    {
        return $this->verbosity;
    }
}
