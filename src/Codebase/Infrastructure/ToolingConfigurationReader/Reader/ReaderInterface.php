<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Codebase\Infrastructure\ToolingConfigurationReader\Reader;

use Codebase\Application\Dto\ConfigurationResponseDto;

interface ReaderInterface
{
    /**
     * @param array<mixed> $configuration
     * @param \Codebase\Application\Dto\ConfigurationResponseDto $configurationResponseDto
     *
     * @return void
     */
    public function read(array $configuration, ConfigurationResponseDto $configurationResponseDto): void;
}
