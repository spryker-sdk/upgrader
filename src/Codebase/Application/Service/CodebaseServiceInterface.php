<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Application\Service;

use Codebase\Application\Dto\CodebaseRequestDto;
use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Application\Dto\ConfigurationRequestDto;
use Codebase\Application\Dto\ConfigurationResponseDto;
use Evaluate\Infrastructure\Configuration\ConfigurationProvider;

interface CodebaseServiceInterface
{
    /**
     * @param \Evaluate\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function parse(ConfigurationProvider $configurationProvider): CodebaseSourceDto;

    /**
     * @param \Codebase\Application\Dto\CodebaseRequestDto $codebaseRequestDto
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function parseSource(CodebaseRequestDto $codebaseRequestDto): CodebaseSourceDto;

    /**
     * @param \Codebase\Application\Dto\ConfigurationRequestDto $configurationRequestDto
     *
     * @return \Codebase\Application\Dto\ConfigurationResponseDto
     */
    public function parseProjectConfiguration(ConfigurationRequestDto $configurationRequestDto): ConfigurationResponseDto;
}
