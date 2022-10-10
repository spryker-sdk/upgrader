<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Codebase\Infrastructure\ToolingConfigurationReader\Reader;

use Codebase\Application\Dto\ConfigurationResponseDto;
use Codebase\Infrastructure\Exception\ProjectConfigurationFileInvalidSyntaxException;
use Codebase\Infrastructure\ToolingConfigurationReader\ToolingConfigurationReader;

class IgnoredRulesReader extends AbstractReader
{
    /**
     * @var string
     */
    public const RULES_KEY = 'rules';

    /**
     * @var string
     */
    public const IGNORE_KEY = 'ignore';

    /**
     * @param array<mixed> $configuration
     *
     * @throws \Codebase\Infrastructure\Exception\ProjectConfigurationFileInvalidSyntaxException
     *
     * @return void
     */
    public function read(array $configuration, ConfigurationResponseDto $configurationResponseDto): void
    {
        if (!isset($configuration[ToolingConfigurationReader::EVALUATOR_KEY][static::RULES_KEY][static::IGNORE_KEY])) {
            return;
        }

        $ignoredRules = $configuration[ToolingConfigurationReader::EVALUATOR_KEY][static::RULES_KEY][static::IGNORE_KEY];

        if (!is_array($ignoredRules) || !$this->isSequentialArrayOfString($ignoredRules)) {
            throw new ProjectConfigurationFileInvalidSyntaxException(
                sprintf(
                    'Value of %s.%s.%s should be array of string',
                    ToolingConfigurationReader::EVALUATOR_KEY,
                    static::RULES_KEY,
                    static::IGNORE_KEY,
                ),
            );
        }

        $configurationResponseDto->setIgnoredRules($ignoredRules);
    }
}
