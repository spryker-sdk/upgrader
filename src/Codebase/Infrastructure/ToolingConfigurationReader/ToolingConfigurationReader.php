<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\ToolingConfigurationReader;

use Codebase\Application\Dto\ConfigurationResponseDto;
use Symfony\Component\Yaml\Yaml;

class ToolingConfigurationReader implements ToolingConfigurationReaderInterface
{
    /**
     * @var array<\Codebase\Infrastructure\ToolingConfigurationReader\Validator\ToolingConfigurationValidatorInterface>
     */
    protected array $validators;

    /**
     * @param array<\Codebase\Infrastructure\ToolingConfigurationReader\Validator\ToolingConfigurationValidatorInterface> $validators
     */
    public function __construct(array $validators)
    {
        $this->validators = $validators;
    }

    /**
     * @param string $configurationFilePath
     *
     * @return \Codebase\Application\Dto\ConfigurationResponseDto
     */
    public function readToolingConfiguration(string $configurationFilePath): ConfigurationResponseDto
    {
        if (!file_exists($configurationFilePath)) {
            return new ConfigurationResponseDto();
        }

        $configuration = Yaml::parseFile($configurationFilePath);
        foreach ($this->validators as $validator) {
            $validator->validate($configuration);
        }

        return new ConfigurationResponseDto($configuration);
    }
}
