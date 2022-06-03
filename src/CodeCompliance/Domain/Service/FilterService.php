<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Service;

class FilterService
{
    /**
     * @var array<\CodeCompliance\Domain\Checks\Filters\FilterInterface>
     */
    protected array $filters = [];

    /**
     * @param array<\CodeCompliance\Domain\Checks\Filters\FilterInterface> $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @param array<\Codebase\Application\Dto\CodebaseInterface> $sources
     * @param array<string> $enabledFilters
     *
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    public function filter(array $sources, array $enabledFilters): array
    {
        foreach ($this->filters as $filter) {
            if (in_array($filter->getFilterName(), $enabledFilters)) {
                $sources = $filter->filter($sources);
            }
        }

        return $sources;
    }
}
