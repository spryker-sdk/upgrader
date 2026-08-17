<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Codebase\Application\Service;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Application\Dto\ConfigurationResponseDto;

interface CodebaseServiceInterface
{
    public function readCodeBase(CodeBaseRequestDto $codebaseRequestDto): CodebaseSourceDto;

    public function readToolingConfiguration(string $configurationFilePath): ConfigurationResponseDto;
}
