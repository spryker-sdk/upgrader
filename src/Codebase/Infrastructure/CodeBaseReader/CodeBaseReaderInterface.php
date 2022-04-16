<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\CodeBaseReader;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Application\Dto\CodebaseSourceDto;

interface CodeBaseReaderInterface
{
    /**
     * @param \Codebase\Application\Dto\CodeBaseRequestDto $codebaseRequestDto
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function readCodeBase(CodeBaseRequestDto $codebaseRequestDto): CodebaseSourceDto;
}
