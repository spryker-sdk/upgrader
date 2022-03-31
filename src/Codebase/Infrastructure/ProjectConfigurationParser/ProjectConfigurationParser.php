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
     * @var string
     */
    public const INVALID_TYPE_ERROR_MESSAGE = 'Value of %s.%s should be array of string';

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

        $projectDirectories = $this->buildProjectDirectories($configurationRequestDto->getSrcDirectory(), $projectPrefixes);

        return new ConfigurationResponseDto($projectPrefixes, $projectDirectories);
    }

    /**
     * @param string $configPath
     *
     * @throws \Exception
     *
     * @return array<string>
     */
    protected function parseProjectPrefixes(string $configPath): array
    {
        if (!file_exists($configPath)) {
            return [self::DEFAULT_PREFIX];
        }

        $configuration = Yaml::parseFile($configPath);
        $projectPrefixes = $configuration[self::UPGRADER_KEY][self::PREFIXES_KEY];

        if (!is_array($projectPrefixes) || !$this->isSequentialArrayOfString($projectPrefixes)) {
            throw new Exception(sprintf(self::INVALID_TYPE_ERROR_MESSAGE, self::UPGRADER_KEY, self::PREFIXES_KEY));
        }

        return $projectPrefixes;
    }

    /**
     * @param array $array
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
