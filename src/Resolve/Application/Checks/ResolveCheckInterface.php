<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Application\Checks;

use Codebase\Application\Dto\CodebaseSourceDto;
use Resolve\Domain\Entity\Message;

interface ResolveCheckInterface
{
    /**
     * @param Message $message
     * @param CodebaseSourceDto $codebaseSourceDto
     *
     * @return Message
     */
    public function run(Message $message, CodebaseSourceDto $codebaseSourceDto): Message;
}
