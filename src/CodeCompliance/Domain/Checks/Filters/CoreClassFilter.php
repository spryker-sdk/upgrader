<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Checks\Filters;

use Codebase\Application\Dto\CodebaseInterface;

class CoreClassFilter implements FilterInterface
{
    /**
     * @var string
     */
    public const CORE_CLASS_FILTER = 'CORE_CLASS_FILTER';

    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return static::CORE_CLASS_FILTER;
    }

    /**
     * @param array<\Codebase\Application\Dto\CodebaseInterface> $sources
     *
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    public function filter(array $sources): array
    {
        return array_filter($sources, function (CodebaseInterface $source) {
            return $source->hasClassNameCoreNamespace();
        });
    }
}
