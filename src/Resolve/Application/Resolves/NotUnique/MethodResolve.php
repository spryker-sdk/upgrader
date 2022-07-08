<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Application\Resolves\NotUnique;

use Codebase\Application\Dto\CodebaseSourceDto;
use Resolve\Application\Resolves\ResolveInterface;
use Resolve\Domain\Resolves\NotUnique\Method;
use Resolve\Domain\Entity\Message;

class MethodResolve implements ResolveInterface
{
    /**
     * @var Method
     */
    protected Method $methodResolve;

    /**
     * @param Method $methodResolve
     */
    public function __construct(Method $methodResolve)
    {
        $this->methodResolve = $methodResolve;
    }

    /**
     * @param Message $message
     * @param CodebaseSourceDto $codebaseSourceDto
     *
     * @return Message
     */
    public function run(Message $message, CodebaseSourceDto $codebaseSourceDto): Message
    {
        $message = $this->methodResolve->setCodebaseSourceDto($codebaseSourceDto)->getResult();
        return new Message($message);
    }
}
