<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Codebase\Infrastructure\Service;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Application\Dto\ConfigurationResponseDto;
use Codebase\Application\Service\CodebaseServiceInterface;
use Codebase\Infrastructure\CodeBaseReader\CodeBaseReaderInterface;
use Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReaderInterface;

/**
 * TODO: remove unused service
 */
class CodebaseService implements CodebaseServiceInterface
{
    protected CodeBaseReaderInterface $codeBaseReader;

    protected ToolingConfigurationReaderInterface $toolingConfigurationReader;

    public function __construct(CodeBaseReaderInterface $codeBaseReader, ToolingConfigurationReaderInterface $toolingConfigurationReader)
    {
        $this->codeBaseReader = $codeBaseReader;
        $this->toolingConfigurationReader = $toolingConfigurationReader;
    }

    public function readCodeBase(CodeBaseRequestDto $codebaseRequestDto): CodebaseSourceDto
    {
        return $this->codeBaseReader->readCodeBase($codebaseRequestDto);
    }

    public function readToolingConfiguration(string $configurationFilePath): ConfigurationResponseDto
    {
        return $this->toolingConfigurationReader->readToolingConfiguration($configurationFilePath);
    }
}
