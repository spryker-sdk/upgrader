<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Codebase\Infrastructure\ToolingConfigurationReader;

use Codebase\Application\Dto\ConfigurationResponseDto;
use Symfony\Component\Yaml\Yaml;

class ToolingConfigurationReader implements ToolingConfigurationReaderInterface
{
    /**
     * @var string
     */
    public const EVALUATOR_KEY = 'evaluator';

    /**
     * @var array<\Codebase\Infrastructure\ToolingConfigurationReader\Reader\ReaderInterface>
     */
    protected array $propertyReaders;

    /**
     * @param array<\Codebase\Infrastructure\ToolingConfigurationReader\Reader\ReaderInterface> $propertyReaders
     */
    public function __construct(array $propertyReaders)
    {
        $this->propertyReaders = $propertyReaders;
    }

    /**
     * @param string $configurationFilePath
     *
     * @return \Codebase\Application\Dto\ConfigurationResponseDto
     */
    public function readToolingConfiguration(string $configurationFilePath): ConfigurationResponseDto
    {
        $configurationResponseDto = new ConfigurationResponseDto();
        if (!file_exists($configurationFilePath)) {
            return $configurationResponseDto;
        }

        $configuration = Yaml::parseFile($configurationFilePath);
        foreach ($this->propertyReaders as $propertyReader) {
            $propertyReader->read($configuration, $configurationResponseDto);
        }

        return $configurationResponseDto;
    }
}
