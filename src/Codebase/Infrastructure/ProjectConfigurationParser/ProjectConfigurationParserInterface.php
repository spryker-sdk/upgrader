<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\ProjectConfigurationParser;

use Codebase\Application\Dto\ConfigurationRequestDto;
use Codebase\Application\Dto\ConfigurationResponseDto;

interface ProjectConfigurationParserInterface
{
    /**
     * @param \Codebase\Application\Dto\ConfigurationRequestDto $configurationRequestDto
     *
     * @return \Codebase\Application\Dto\ConfigurationResponseDto
     */
    public function parseConfiguration(ConfigurationRequestDto $configurationRequestDto): ConfigurationResponseDto;
}
