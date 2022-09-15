<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Codebase\Application\Dto;

class ConfigurationResponseDto
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
     * @var array<string>
     */
    protected const DEFAULT_PREFIXES = ['Pyz'];

    /**
     * @var array<mixed>
     */
    protected array $configuration;

    /**
     * @param array<mixed> $configuration
     */
    public function __construct(array $configuration = [])
    {
        $this->configuration = $configuration;
    }

    /**
     * @return array<string>
     */
    public function getProjectPrefixes(): array
    {
        if (isset($this->configuration[static::UPGRADER_KEY][static::PREFIXES_KEY])) {
            return $this->configuration[static::UPGRADER_KEY][static::PREFIXES_KEY];
        }

        return static::DEFAULT_PREFIXES;
    }
}
