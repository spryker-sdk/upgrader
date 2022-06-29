<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Infrastructure\Service;

use Codebase\Application\Dto\ClassCodebaseDto;
use Codebase\Infrastructure\SourceParser\Parser\PhpParserInterface;
use Resolve\Domain\Service\CodeBaseServiceInterface;

class CodeBaseService implements CodeBaseServiceInterface
{
    /**
     * @var \Codebase\Infrastructure\SourceParser\Parser\PhpParserInterface
     */
    protected PhpParserInterface $phpParser;

    /**
     * @param \Codebase\Infrastructure\SourceParser\Parser\PhpParserInterface $phpParser
     */
    public function __construct(PhpParserInterface $phpParser)
    {
        $this->phpParser = $phpParser;
    }

    /**
     * @param string $classNamespace
     * @param array<string> $projectPrefixes
     * @param array<string> $coreNamespaces
     *
     * @return \Codebase\Application\Dto\ClassCodebaseDto|null
     */
    public function parsePhpClass(string $classNamespace, array $projectPrefixes, array $coreNamespaces = []): ?ClassCodebaseDto
    {
        return $this->phpParser->parseClass($classNamespace, $projectPrefixes, $coreNamespaces);
    }
}
