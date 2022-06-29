<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Application\Checks\NotUnique;

use Codebase\Application\Dto\CodebaseSourceDto;
use Resolve\Domain\Checks\NotUnique\Constant;
use Resolve\Application\Checks\ResolveCheckInterface;
use Resolve\Domain\Entity\Message;

class ConstantResolve implements ResolveCheckInterface
{
    /**
     */
    protected Constant $constantCheck;

    /**
     * @param Constant $constantCheck
     */
    public function __construct(Constant $constantCheck)
    {
        $this->constantCheck = $constantCheck;
    }

    /**
     * @param Message $message
     * @param CodebaseSourceDto $codebaseSourceDto
     *
     * @return Message
     */
    public function run(Message $message, CodebaseSourceDto $codebaseSourceDto): Message
    {
        return $this->constantCheck
            ->setCodebaseSourceDto($codebaseSourceDto)
            ->getResult();
        $message->getMessage();
    }
}
