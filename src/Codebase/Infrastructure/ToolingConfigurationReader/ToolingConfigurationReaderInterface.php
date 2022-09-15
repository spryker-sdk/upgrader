<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\ToolingConfigurationReader;

use Codebase\Application\Dto\ConfigurationResponseDto;

interface ToolingConfigurationReaderInterface
{
    /**
     * @param string $configurationFilePath
     *
     * @return \Codebase\Application\Dto\ConfigurationResponseDto
     */
    public function readToolingConfiguration(string $configurationFilePath): ConfigurationResponseDto;
}
