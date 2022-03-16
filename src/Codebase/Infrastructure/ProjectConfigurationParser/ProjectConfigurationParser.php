<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\ProjectConfigurationParser;

use Codebase\Application\Dto\ConfigurationRequestDto;
use Codebase\Application\Dto\ConfigurationResponseDto;
use Symfony\Component\Yaml\Yaml;

class ProjectConfigurationParser implements ProjectConfigurationParserInterface
{
    /**
     * @var string
     */
    public const UPGRADER_KEY = 'upgrader';

    /**
     * @var string
     */
    public const PREFIXES_KEY = 'prefixes';

    /**
     * @var string
     */
    public const DEFAULT_PREFIX = 'Pyz';

    /**
     * @param \Codebase\Application\Dto\ConfigurationRequestDto $configurationRequestDto
     *
     * @return \Codebase\Application\Dto\ConfigurationResponseDto
     */
    public function parseConfiguration(ConfigurationRequestDto $configurationRequestDto): ConfigurationResponseDto
    {
        $fileContent = $this->parseFile($configurationRequestDto->getConfigurationFilePath());

        $projectPrefixes = $this->getProjectPrefixes($fileContent);
        $projectDirectories = $this->buildProjectDirectories($configurationRequestDto->getSrcDirectory(), $projectPrefixes);

        return new ConfigurationResponseDto($projectPrefixes, $projectDirectories);
    }

    /**
     * @param string $path
     *
     * @return array
     */
    protected function parseFile(string $path): array
    {
        if (!file_exists($path)) {
            return [];
        }

        return Yaml::parseFile($path);
    }

    /**
     * @param array $configuration
     *
     * @return array<string>
     */
    protected function getProjectPrefixes(array $configuration): array
    {
        $upgraderConfig = $configuration[self::UPGRADER_KEY] ?? null;
        if (!$upgraderConfig) {
            return [self::DEFAULT_PREFIX];
        }

        $projectPrefixes = $upgraderConfig[self::PREFIXES_KEY] ?? null;
        if (!$projectPrefixes) {
            return [self::DEFAULT_PREFIX];
        }

        return $projectPrefixes;
    }

    /**
     * @param string $srcDirectory
     * @param array $projectPrefixes
     *
     * @return array
     */
    protected function buildProjectDirectories(string $srcDirectory, array $projectPrefixes): array
    {
        $projectDirectories = [];

        foreach ($projectPrefixes as $prefix) {
            $projectDirectories[] = $srcDirectory . $prefix . DIRECTORY_SEPARATOR;
        }

        return $projectDirectories;
    }
}
