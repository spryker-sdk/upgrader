<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Application\Service;

use Codebase\Application\Dto\CodebaseSourceDto;
use Resolve\Domain\Entity\Message;

interface ResolveServiceInterface
{
    /**
     * @param CodebaseSourceDto $codebaseSourceDto
     *
     * @return Message
     */
    public function resolve(CodebaseSourceDto $codebaseSourceDto): Message;
}
