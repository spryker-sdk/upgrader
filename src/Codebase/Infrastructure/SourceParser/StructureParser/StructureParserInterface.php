<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Codebase\Infrastructure\SourceParser\StructureParser;

use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Application\Dto\SourceParserRequestDto;

interface StructureParserInterface
{
    public function parse(SourceParserRequestDto $codebaseRequestDto, CodebaseSourceDto $codebaseSourceDto): CodebaseSourceDto;
}
