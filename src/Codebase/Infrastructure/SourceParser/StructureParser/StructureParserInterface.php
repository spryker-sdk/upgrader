<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\SourceParser\StructureParser;

use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Application\Dto\SourceParserRequestDto;

interface StructureParserInterface
{
    /**
     * @param \Codebase\Application\Dto\SourceParserRequestDto $codebaseRequestDto
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function parse(SourceParserRequestDto $codebaseRequestDto, CodebaseSourceDto $codebaseSourceDto): CodebaseSourceDto;
}
