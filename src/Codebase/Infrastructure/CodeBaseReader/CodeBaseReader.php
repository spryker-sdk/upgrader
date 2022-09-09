<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\CodeBaseReader;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Infrastructure\CodeBaseReader\Mapper\ModuleOptionMapperInterface;
use Codebase\Infrastructure\CodeBaseReader\Mapper\SourceParserRequestMapperInterface;
use Codebase\Infrastructure\SourceParser\SourceParserInterface;
use Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReaderInterface;

class CodeBaseReader implements CodeBaseReaderInterface
{
    /**
     * @var \Codebase\Infrastructure\SourceParser\SourceParserInterface
     */
    protected SourceParserInterface $sourceParser;

    /**
     * @var \Codebase\Infrastructure\CodeBaseReader\Mapper\ModuleOptionMapperInterface
     */
    protected ModuleOptionMapperInterface $moduleOptionMapper;

    /**
     * @var \Codebase\Infrastructure\CodeBaseReader\Mapper\SourceParserRequestMapperInterface
     */
    protected SourceParserRequestMapperInterface $sourceParserRequestMapper;

    /**
     * @var \Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReaderInterface
     */
    protected ToolingConfigurationReaderInterface $toolingConfigurationReader;

    /**
     * @param \Codebase\Infrastructure\SourceParser\SourceParserInterface $sourceParser
     * @param \Codebase\Infrastructure\CodeBaseReader\Mapper\ModuleOptionMapperInterface $moduleOptionMapper
     * @param \Codebase\Infrastructure\CodeBaseReader\Mapper\SourceParserRequestMapperInterface $sourceParserRequestMapper
     * @param \Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReaderInterface $toolingConfigurationReader
     */
    public function __construct(
        SourceParserInterface $sourceParser,
        ModuleOptionMapperInterface $moduleOptionMapper,
        SourceParserRequestMapperInterface $sourceParserRequestMapper,
        ToolingConfigurationReaderInterface $toolingConfigurationReader
    ) {
        $this->sourceParser = $sourceParser;
        $this->moduleOptionMapper = $moduleOptionMapper;
        $this->sourceParserRequestMapper = $sourceParserRequestMapper;
        $this->toolingConfigurationReader = $toolingConfigurationReader;
    }

    /**
     * @param \Codebase\Application\Dto\CodeBaseRequestDto $codebaseRequestDto
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function readCodeBase(CodeBaseRequestDto $codebaseRequestDto): CodebaseSourceDto
    {
        $configurationFilePath = $codebaseRequestDto->getToolingConfigurationFilePath();
        $configurationResponseDto = $this->toolingConfigurationReader->readToolingConfiguration($configurationFilePath);
        $optionModules = $this->moduleOptionMapper->mapToModuleList($codebaseRequestDto->getModuleOption());

        $sourceParserRequest = $this->sourceParserRequestMapper->mapToSourceParserRequest(
            $codebaseRequestDto,
            $configurationResponseDto,
            $optionModules,
        );

        return $this->sourceParser->parseSource($sourceParserRequest);
    }
}
