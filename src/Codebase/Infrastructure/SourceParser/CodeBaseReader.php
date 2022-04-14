<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\SourceParser;

use Codebase\Application\Dto\CodebaseRequestDto;
use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReaderInterface;

class CodeBaseReader
{
    /**
     * @var \Codebase\Infrastructure\SourceParser\SourceParser
     */
    protected $sourceParser;

    /**
     * @var \Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReaderInterface
     */
    protected ToolingConfigurationReaderInterface $toolingConfigurationReader;

    /**
     * @param \Codebase\Infrastructure\SourceParser\SourceParser $parser
     * @param \Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReaderInterface $toolingConfigurationReader
     */
    public function __construct(SourceParser $parser, ToolingConfigurationReaderInterface $toolingConfigurationReader)
    {
        $this->sourceParser = $parser;
        $this->toolingConfigurationReader = $toolingConfigurationReader;
    }

    /**
     * @param \Codebase\Application\Dto\CodebaseRequestDto $codebaseRequestDto
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function readCodeBase(CodebaseRequestDto $codebaseRequestDto): CodebaseSourceDto
    {
        $configurationFilePath = $codebaseRequestDto->getToolingConfigurationPath();
        $configurationResponseDto = $this->toolingConfigurationReader->readConfiguration($configurationFilePath);
        $codebaseRequestDto->setProjectPrefixes($configurationResponseDto->getProjectPrefixes());

        return $this->sourceParser->parseSource($codebaseRequestDto);
    }
}
