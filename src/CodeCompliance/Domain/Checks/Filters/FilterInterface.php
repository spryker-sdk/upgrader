<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\Filters;

interface FilterInterface
{
    /**
     * @return string
     */
    public function getFilterName(): string;

    /**
     * @param array $sources
     *
     * @return array
     */
    public function filter(array $sources): array;
}
