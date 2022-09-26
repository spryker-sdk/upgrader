<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Checks\Filters;

use Codebase\Application\Dto\CodebaseInterface;

class IgnoreListFilter extends AbstractIgnoreListFilter
{
    /**
     * @var string
     */
    public const IGNORE_LIST_FILTER = 'IGNORE_LIST_FILTER';

    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return static::IGNORE_LIST_FILTER;
    }

    /**
     * @param array<\Codebase\Application\Dto\CodebaseInterface> $sources
     *
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    public function filter(array $sources): array
    {
        return array_filter($sources, function (CodebaseInterface $source) {
            return !$this->isClassFromIgnoreList((string)$source->getClassName());
        });
    }
}
