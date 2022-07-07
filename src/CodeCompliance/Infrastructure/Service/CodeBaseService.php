<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Infrastructure\Service;

use Codebase\Application\Dto\ClassCodebaseDto;
use Codebase\Infrastructure\SourceParser\FileParser\PhpFileParserInterface;
use CodeCompliance\Domain\Service\CodeBaseServiceInterface;

class CodeBaseService implements CodeBaseServiceInterface
{
    /**
     * @var \Codebase\Infrastructure\SourceParser\FileParser\PhpFileParserInterface
     */
    protected PhpFileParserInterface $phpParser;

    /**
     * @param \Codebase\Infrastructure\SourceParser\FileParser\PhpFileParserInterface $phpParser
     */
    public function __construct(PhpFileParserInterface $phpParser)
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
