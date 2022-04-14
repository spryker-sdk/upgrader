<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\Service;

use Codebase\Application\Dto\CodebaseRequestDto;
use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Application\Dto\ConfigurationResponseDto;
use Codebase\Application\Service\CodebaseServiceInterface;
use Codebase\Infrastructure\SourceParser\CodeBaseReader;
use Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReaderInterface;

class CodebaseService implements CodebaseServiceInterface
{
    /**
     * @var \Codebase\Infrastructure\SourceParser\CodeBaseReader
     */
    protected $codeBaseReader;

    /**
     * @var \Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReaderInterface
     */
    protected ToolingConfigurationReaderInterface $projectConfigurationParser;

    /**
     * @param \Codebase\Infrastructure\SourceParser\CodeBaseReader $codeBaseReader
     * @param \Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReaderInterface $projectConfigurationParser
     */
    public function __construct(CodeBaseReader $codeBaseReader, ToolingConfigurationReaderInterface $projectConfigurationParser)
    {
        $this->codeBaseReader = $codeBaseReader;
        $this->projectConfigurationParser = $projectConfigurationParser;
    }

    /**
     * @param \Codebase\Application\Dto\CodebaseRequestDto $codebaseRequestDto
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function parseSource(CodebaseRequestDto $codebaseRequestDto): CodebaseSourceDto
    {
        return $this->codeBaseReader->readCodeBase($codebaseRequestDto);
    }

    /**
     * @param string $configurationFilePath
     *
     * @return \Codebase\Application\Dto\ConfigurationResponseDto
     */
    public function readToolingConfiguration(string $configurationFilePath): ConfigurationResponseDto
    {
        return $this->projectConfigurationParser->readConfiguration($configurationFilePath);
    }
}
