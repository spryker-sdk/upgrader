<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\Service;

use Codebase\Application\Dto\CodebaseRequestDto;
use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Application\Service\CodebaseServiceInterface;
use Codebase\Infrastructure\SourceParser\SourceParser;

class CodebaseService implements CodebaseServiceInterface
{
    /**
     * @var \Codebase\Infrastructure\SourceParser\SourceParser
     */
    protected $sourceParser;

    /**
     * @param \Codebase\Infrastructure\SourceParser\SourceParser $sourceParser
     */
    public function __construct(SourceParser $sourceParser)
    {
        $this->sourceParser = $sourceParser;
    }

    /**
     * @param \Codebase\Application\Dto\CodebaseRequestDto $codebaseRequestDto
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function parseSource(CodebaseRequestDto $codebaseRequestDto): CodebaseSourceDto
    {
        return $this->sourceParser->parseSource($codebaseRequestDto);
    }
}
