<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\ProjectConfigurationParser;

use Codebase\Application\Dto\ConfigurationRequestDto;
use Codebase\Application\Dto\ConfigurationResponseDto;
use Codebase\Infrastructure\Exception\ProjectConfigurationFileInvalidSyntaxException;
use Exception;
use Symfony\Component\Yaml\Yaml;

class ProjectConfigurationParser implements ProjectConfigurationParserInterface
{
    /**
     * @var string
     */
    protected const UPGRADER_KEY = 'upgrader';

    /**
     * @var string
     */
    protected const PREFIXES_KEY = 'prefixes';

    /**
     * @var string
     */
    protected const DEFAULT_PREFIX = 'Pyz';

    /**
     * @param \Codebase\Application\Dto\ConfigurationRequestDto $configurationRequestDto
     *
     * @throws \Codebase\Infrastructure\Exception\ProjectConfigurationFileInvalidSyntaxException
     *
     * @return \Codebase\Application\Dto\ConfigurationResponseDto
     */
    public function parseConfiguration(ConfigurationRequestDto $configurationRequestDto): ConfigurationResponseDto
    {
        $configPath = $configurationRequestDto->getConfigurationFilePath();
        try {
            $projectPrefixes = $this->parseProjectPrefixes($configPath);
        } catch (Exception $exception) {
            throw new ProjectConfigurationFileInvalidSyntaxException($configPath, $exception->getMessage());
        }

        $projectDirectories = $this->getProjectDirectories($configurationRequestDto->getSrcDirectory(), $projectPrefixes);

        return new ConfigurationResponseDto($projectPrefixes, $projectDirectories);
    }

    /**
     * @param string $configPath
     *
     * @throws \Codebase\Infrastructure\Exception\ProjectConfigurationFileInvalidSyntaxException
     *
     * @return array<string>
     */
    protected function parseProjectPrefixes(string $configPath): array
    {
        if (!file_exists($configPath)) {
            return [static::DEFAULT_PREFIX];
        }

        $configuration = Yaml::parseFile($configPath);
        $projectPrefixes = $configuration[static::UPGRADER_KEY][static::PREFIXES_KEY];

        if (!is_array($projectPrefixes) || !$this->isSequentialArrayOfString($projectPrefixes)) {
            throw new ProjectConfigurationFileInvalidSyntaxException(
                $configPath,
                sprintf('Value of %s.%s should be array of string', static::UPGRADER_KEY, static::PREFIXES_KEY),
            );
        }

        return $projectPrefixes;
    }

    /**
     * @param array<string> $array
     *
     * @return bool
     */
    protected function isSequentialArrayOfString(array $array): bool
    {
        if (count(array_filter(array_keys($array), 'is_string'))) {
            return false;
        }

        if (count(array_filter(array_values($array), 'is_string')) !== count($array)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $srcDirectory
     * @param array<string> $projectPrefixes
     *
     * @return array<string>
     */
    protected function getProjectDirectories(string $srcDirectory, array $projectPrefixes): array
    {
        $projectDirectories = [];

        foreach ($projectPrefixes as $prefix) {
            $projectDirectories[] = $srcDirectory . $prefix . DIRECTORY_SEPARATOR;
        }

        return $projectDirectories;
    }
}
