<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Domain\Resolves\Filters;

use Codebase\Application\Dto\CodebaseInterface;

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
     * @param array<\Codebase\Application\Dto\CodebaseInterface> $sources
     *
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    public function filter(array $sources): array
    {
        return array_filter($sources, function (CodebaseInterface $source) {
            return (bool)$source->getCoreParent();
        });
    }
}
