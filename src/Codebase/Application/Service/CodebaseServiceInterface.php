<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Application\Service;

use Codebase\Application\Dto\CodebaseRequestDto;
use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Application\Dto\ConfigurationResponseDto;

interface CodebaseServiceInterface
{
    /**
     * @param \Codebase\Application\Dto\CodebaseRequestDto $codebaseRequestDto
     *
     * @return \Codebase\Application\Dto\CodebaseSourceDto
     */
    public function parseSource(CodebaseRequestDto $codebaseRequestDto): CodebaseSourceDto;

    /**
     * @param string $configurationFilePath
     *
     * @return \Codebase\Application\Dto\ConfigurationResponseDto
     */
    public function readToolingConfiguration(string $configurationFilePath): ConfigurationResponseDto;
}
