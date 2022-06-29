<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Domain\Entity;

use SprykerSdk\SdkContracts\Entity\MessageInterface;

class Message implements MessageInterface
{
    /**
     * @var string
     */
    protected string $message = '';

    /**
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    public function getVerbosity(): int
    {
        return 1;
    }
}
