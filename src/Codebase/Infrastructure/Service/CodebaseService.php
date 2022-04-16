<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\Service;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Application\Dto\ConfigurationResponseDto;
use Codebase\Application\Service\CodebaseServiceInterface;
use Codebase\Infrastructure\CodeBaseReader\CodeBaseReaderInterface;
use Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReaderInterface;

class CodebaseService implements CodebaseServiceInterface
{
    /**
     * @var \Codebase\Infrastructure\CodeBaseReader\CodeBaseReaderInterface
     */
    protected CodeBaseReaderInterface $codeBaseReader;

    /**
     * @var \Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReaderInterface
     */
    protected ToolingConfigurationReaderInterface $toolingConfigurationReader;

    /**
     * @param \Codebase\Infrastructure\CodeBaseReader\CodeBaseReaderInterface $codeBaseReader
     * @param \Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReaderInterface $toolingConfigurationReader
     */
    public function __construct(CodeBaseReaderInterface $codeBaseReader, ToolingConfigurationReaderInterface $toolingConfigurationReader)
    {
        $this->codeBaseReader = $codeBaseReader;
        $this->toolingConfigurationReader = $toolingConfigurationReader;
    }

    /**
     * @param \Codebase\Application\Dto\CodeBaseRequestDto $codebaseRequestDto
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function readCodeBase(CodeBaseRequestDto $codebaseRequestDto): CodebaseSourceDto
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
        return $this->toolingConfigurationReader->readConfiguration($configurationFilePath);
    }
}
