<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\CodeBaseReader;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Infrastructure\SourceParser\SourceParserInterface;
use Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReaderInterface;

class CodeBaseReader implements CodeBaseReaderInterface
{
    /**
     * @var \Codebase\Infrastructure\SourceParser\SourceParserInterface
     */
    protected SourceParserInterface $sourceParser;

    /**
     * @var \Codebase\Infrastructure\CodeBaseReader\SourceParserRequestBuilderInterface
     */
    protected SourceParserRequestBuilderInterface $sourceParserRequestBuilder;

    /**
     * @var \Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReaderInterface
     */
    protected ToolingConfigurationReaderInterface $toolingConfigurationReader;

    /**
     * @param \Codebase\Infrastructure\SourceParser\SourceParserInterface $sourceParser
     * @param \Codebase\Infrastructure\CodeBaseReader\SourceParserRequestBuilderInterface $sourceParserRequestBuilder
     * @param \Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReaderInterface $toolingConfigurationReader
     */
    public function __construct(
        SourceParserInterface $sourceParser,
        SourceParserRequestBuilderInterface $sourceParserRequestBuilder,
        ToolingConfigurationReaderInterface $toolingConfigurationReader
    ) {
        $this->sourceParser = $sourceParser;
        $this->sourceParserRequestBuilder = $sourceParserRequestBuilder;
        $this->toolingConfigurationReader = $toolingConfigurationReader;
    }

    /**
     * @param \Codebase\Application\Dto\CodeBaseRequestDto $codebaseRequestDto
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function readCodeBase(CodeBaseRequestDto $codebaseRequestDto): CodebaseSourceDto
    {
        $configurationFilePath = $codebaseRequestDto->getToolingConfigurationPath();
        $configurationResponseDto = $this->toolingConfigurationReader->readConfiguration($configurationFilePath);
        $sourceParserRequest = $this->sourceParserRequestBuilder->getSourceParserRequest($codebaseRequestDto, $configurationResponseDto);

        return $this->sourceParser->parseSource($sourceParserRequest);
    }
}
