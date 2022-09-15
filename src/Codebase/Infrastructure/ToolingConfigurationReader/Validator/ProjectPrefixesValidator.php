<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\ToolingConfigurationReader\Validator;

use Codebase\Application\Dto\ConfigurationResponseDto;
use Codebase\Infrastructure\Exception\ProjectConfigurationFileInvalidSyntaxException;

class ProjectPrefixesValidator implements ToolingConfigurationValidatorInterface
{
    /**
     * @param array<mixed> $configuration
     *
     * @throws \Codebase\Infrastructure\Exception\ProjectConfigurationFileInvalidSyntaxException
     *
     * @return void
     */
    public function validate(array $configuration): void
    {
        if (!isset($configuration[ConfigurationResponseDto::UPGRADER_KEY][ConfigurationResponseDto::PREFIXES_KEY])) {
            throw new ProjectConfigurationFileInvalidSyntaxException(
                sprintf('Key %s.%s not exist', ConfigurationResponseDto::UPGRADER_KEY, ConfigurationResponseDto::PREFIXES_KEY),
            );
        }

        $projectPrefixes = $configuration[ConfigurationResponseDto::UPGRADER_KEY][ConfigurationResponseDto::PREFIXES_KEY];

        if (!is_array($projectPrefixes) || !$this->isSequentialArrayOfString($projectPrefixes)) {
            throw new ProjectConfigurationFileInvalidSyntaxException(
                sprintf('Value of %s.%s should be array of string', ConfigurationResponseDto::UPGRADER_KEY, ConfigurationResponseDto::PREFIXES_KEY),
            );
        }
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
}
