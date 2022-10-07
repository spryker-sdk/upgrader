<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Infrastructure\Adapter;

use Codebase\Application\Dto\ClassCodebaseDto;
use Codebase\Application\Dto\ConfigurationResponseDto;
use Codebase\Infrastructure\Service\CodebaseService;
use Codebase\Infrastructure\SourceParser\FileParser\PhpFileParserInterface;
use CodeCompliance\Domain\Service\CodeBaseServiceInterface;
use Upgrader\Configuration\ConfigurationProvider;

class CodeBaseAdapter implements CodeBaseServiceInterface
{
    /**
     * @var \Upgrader\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var CodebaseService
     */
    protected CodebaseService $codebaseService;

    /**
     * @var \Codebase\Infrastructure\SourceParser\FileParser\PhpFileParserInterface
     */
    protected PhpFileParserInterface $phpParser;

    /**
     * @param ConfigurationProvider $configurationProvider
     * @param PhpFileParserInterface $phpParser
     * @param CodebaseService $codebaseService
     */
    public function __construct(
        ConfigurationProvider $configurationProvider,
        PhpFileParserInterface $phpParser,
        CodebaseService $codebaseService
    )
    {
        $this->configurationProvider = $configurationProvider;
        $this->phpParser = $phpParser;
        $this->codebaseService = $codebaseService;
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

    /**
     * @return ConfigurationResponseDto
     */
    public function readToolingConfiguration(): ConfigurationResponseDto
    {
        return $this->codebaseService->readToolingConfiguration(
            $this->configurationProvider->getToolingConfigurationFilePath()
        );
    }
}
