<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Codebase\Infrastructure\CodeBaseReader\Mapper;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Application\Dto\ConfigurationResponseDto;
use Codebase\Application\Dto\SourceParserRequestDto;

interface SourceParserRequestMapperInterface
{
    /**
     * @param array<\Codebase\Application\Dto\ModuleDto> $modules
     */
    public function mapToSourceParserRequest(
        CodeBaseRequestDto $codebaseRequestDto,
        ConfigurationResponseDto $configurationResponseDto,
        array $modules
    ): SourceParserRequestDto;
}
