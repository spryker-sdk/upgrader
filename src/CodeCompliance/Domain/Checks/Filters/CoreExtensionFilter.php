<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\Filters;

use Codebase\Application\Dto\CodebaseInterface;

class CoreExtensionFilter implements FilterInterface
{
    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return Filters::CORE_EXTENSION_FILTER;
    }

    /**
     * @param array $sources
     *
     * @return array
     */
    public function filter(array $sources): array
    {
        return array_filter($sources, function (CodebaseInterface $source) {
            return (bool)$source->getCoreParent();
        });
    }
}
