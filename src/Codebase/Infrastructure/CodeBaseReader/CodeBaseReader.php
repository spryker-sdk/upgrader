<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Codebase\Infrastructure\CodeBaseReader;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Infrastructure\CodeBaseReader\Mapper\ModuleOptionMapperInterface;
use Codebase\Infrastructure\CodeBaseReader\Mapper\SourceParserRequestMapperInterface;
use Codebase\Infrastructure\SourceParser\SourceParserInterface;
use Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReaderInterface;

class CodeBaseReader implements CodeBaseReaderInterface
{
    protected SourceParserInterface $sourceParser;

    protected ModuleOptionMapperInterface $moduleOptionMapper;

    protected SourceParserRequestMapperInterface $sourceParserRequestMapper;

    protected ToolingConfigurationReaderInterface $toolingConfigurationReader;

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
