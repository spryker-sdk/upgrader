<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\Filters;

use Codebase\Application\Dto\ClassCodebaseDto;

class CoreExtensionFilter implements FilterInterface
{
    /**
     * @var string
     */
    public const CORE_EXTENSION_FILTER = 'CORE_EXTENSION_FILTER';

    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return static::CORE_EXTENSION_FILTER;
    }

    /**
     * @param array<\Codebase\Application\Dto\ClassCodebaseDto> $sources
     *
     * @return array<\Codebase\Application\Dto\ClassCodebaseDto>
     */
    public function filter(array $sources): array
    {
        return array_filter($sources, function (ClassCodebaseDto $source) {
            return (bool)$source->getCoreParent();
        });
    }
}
