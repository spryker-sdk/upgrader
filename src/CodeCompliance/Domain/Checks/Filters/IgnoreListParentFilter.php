<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Checks\Filters;

use Codebase\Application\Dto\CodebaseInterface;

class IgnoreListParentFilter extends AbstractIgnoreListFilter
{
    /**
     * @var string
     */
    public const IGNORE_LIST_PARENT_FILTER = 'IGNORE_LIST_PARENT_FILTER';

    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return static::IGNORE_LIST_PARENT_FILTER;
    }

    /**
     * @param array<\Codebase\Application\Dto\CodebaseInterface> $sources
     *
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    public function filter(array $sources): array
    {
        return array_filter($sources, function (CodebaseInterface $source) {
            $coreParent = $source->getCoreParent();

            return ($coreParent && !$this->isClassFromIgnoreList((string)$coreParent->getClassName()));
        });
    }
}
