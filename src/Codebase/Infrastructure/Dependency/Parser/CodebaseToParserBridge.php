<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Codebase\Infrastructure\Dependency\Parser;

use PhpParser\ParserFactory;

class CodebaseToParserBridge implements CodebaseToParserInterface
{
    /**
     * @var \PhpParser\Parser
     */
    protected $parser;

    public function __construct(ParserFactory $parserFactory)
    {
        $this->parser = $parserFactory->createForHostVersion();
    }

    /**
     * @return array<\PhpParser\Node\Stmt>|null
     */
    public function parse(string $dataToParse): ?array
    {
        return $this->parser->parse($dataToParse);
    }
}
