<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\Dependency\Parser;

use PhpParser\ParserFactory;

class CodebaseToParserBridge implements CodebaseToParserInterface
{
    /**
     * @var \PhpParser\Parser
     */
    protected $parser;

    /**
     * @param \PhpParser\ParserFactory $parserFactory
     */
    public function __construct(ParserFactory $parserFactory)
    {
        $this->parser = $parserFactory->create(ParserFactory::ONLY_PHP7);
    }

    /**
     * @param string $dataToParse
     *
     * @return array<\PhpParser\Node\Stmt>|null
     */
    public function parse(string $dataToParse): ?array
    {
        return $this->parser->parse($dataToParse);
    }
}
