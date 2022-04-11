<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\Service;

use Codebase\Application\Dto\CodebaseRequestDto;
use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Application\Dto\ConfigurationRequestDto;
use Codebase\Application\Dto\ConfigurationResponseDto;
use Codebase\Application\Service\CodebaseServiceInterface;
use Codebase\Infrastructure\ProjectConfigurationParser\ProjectConfigurationParserInterface;
use Codebase\Infrastructure\SourceParser\SourceParser;
use Evaluate\Infrastructure\Configuration\ConfigurationProvider;

class CodebaseService implements CodebaseServiceInterface
{
    /**
     * @var \Codebase\Infrastructure\SourceParser\SourceParser
     */
    protected $sourceParser;

    /**
     * @var \Codebase\Infrastructure\ProjectConfigurationParser\ProjectConfigurationParserInterface
     */
    protected ProjectConfigurationParserInterface $projectConfigurationParser;

    /**
     * @param \Codebase\Infrastructure\SourceParser\SourceParser $sourceParser
     * @param \Codebase\Infrastructure\ProjectConfigurationParser\ProjectConfigurationParserInterface $projectConfigurationParser
     */
    public function __construct(SourceParser $sourceParser, ProjectConfigurationParserInterface $projectConfigurationParser)
    {
        $this->sourceParser = $sourceParser;
        $this->projectConfigurationParser = $projectConfigurationParser;
    }

    /**
     * @param \Evaluate\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function parse(ConfigurationProvider $configurationProvider): CodebaseSourceDto
    {
        $projectConfigurationRequest = new ConfigurationRequestDto(
            $configurationProvider->getToolingConfiguration(),
            $configurationProvider->getSrcDirectory(),
        );
        $projectConfiguration = $this->parseProjectConfiguration($projectConfigurationRequest);
        $codebaseRequestDto = new CodebaseRequestDto(
            $projectConfiguration->getProjectDirectories(),
            $configurationProvider->getCoreDirectory(),
            $configurationProvider->getCoreNamespaces(),
            $projectConfiguration->getProjectPrefixes(),
            $configurationProvider->getIgnoreSources(),
        );

        return $this->parseSource($codebaseRequestDto);
    }

    /**
     * @param \Codebase\Application\Dto\CodebaseRequestDto $codebaseRequestDto
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function parseSource(CodebaseRequestDto $codebaseRequestDto): CodebaseSourceDto
    {
        return $this->sourceParser->parseSource($codebaseRequestDto);
    }

    /**
     * @param \Codebase\Application\Dto\ConfigurationRequestDto $configurationRequestDto
     *
     * @return \Codebase\Application\Dto\ConfigurationResponseDto
     */
    public function parseProjectConfiguration(ConfigurationRequestDto $configurationRequestDto): ConfigurationResponseDto
    {
        return $this->projectConfigurationParser->parseConfiguration($configurationRequestDto);
    }
}
