<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Entity;

use SprykerSdk\SdkContracts\Entity\MessageInterface;

class Message implements MessageInterface
{
    /**
     * @var string
     */
    protected string $message = '';

    /**
     * @var int
     */
    protected int $verbosity = 1;

    /**
     * @param string $message
     * @param int $verbosity
     */
    public function __construct(string $message = '', int $verbosity = 1)
    {
        $this->message = $message;
        $this->verbosity = $verbosity;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getVerbosity(): int
    {
        return $this->verbosity;
    }
}
