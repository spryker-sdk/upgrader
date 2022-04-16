<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\CodeBaseReader;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Application\Dto\ConfigurationResponseDto;
use Codebase\Application\Dto\SourceParserRequestDto;

interface SourceParserRequestMapperInterface
{
    /**
     * @param \Codebase\Application\Dto\CodeBaseRequestDto $codebaseRequestDto
     * @param \Codebase\Application\Dto\ConfigurationResponseDto $configurationResponseDto
     *
     * @return \Codebase\Application\Dto\SourceParserRequestDto
     */
    public function getSourceParserRequest(
        CodeBaseRequestDto $codebaseRequestDto,
        ConfigurationResponseDto $configurationResponseDto
    ): SourceParserRequestDto;
}
