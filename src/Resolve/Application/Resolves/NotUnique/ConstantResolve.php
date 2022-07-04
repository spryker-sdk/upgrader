<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Application\Resolves\NotUnique;

use Codebase\Application\Dto\CodebaseSourceDto;
use Resolve\Domain\Resolves\NotUnique\Constant;
use Resolve\Application\Resolves\ResolveInterface;
use Resolve\Domain\Entity\Message;

class ConstantResolve implements ResolveInterface
{
    /**
     * @var Constant
     */
    protected Constant $constantResolve;

    /**
     * @param Constant $constantResolve
     */
    public function __construct(Constant $constantResolve)
    {
        $this->constantResolve = $constantResolve;
    }

    /**
     * @param Message $message
     * @param CodebaseSourceDto $codebaseSourceDto
     *
     * @return Message
     */
    public function run(Message $message, CodebaseSourceDto $codebaseSourceDto): Message
    {
        $message = $this->constantResolve->setCodebaseSourceDto($codebaseSourceDto)->getResult();
        return new Message($message);
    }
}
