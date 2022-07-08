<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Domain\Service;

use Codebase\Application\Dto\CodebaseInterface;

class FilterService
{
    /**
     * @var array<FilterInterface>
     */
    protected $filters = [];

    /**
     * @param array<FilterInterface> $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @param array<CodebaseInterface> $sources
     * @param array<string> $enabledFilters
     *
     * @return array<CodebaseInterface>
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
