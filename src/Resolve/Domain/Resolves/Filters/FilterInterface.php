<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Domain\Resolves\Filters;

use Codebase\Application\Dto\CodebaseInterface;

interface FilterInterface
{
    /**
     * @return string
     */
    public function getFilterName(): string;

    /**
     * @param array<CodebaseInterface> $sources
     *
     * @return array<CodebaseInterface>
     */
    public function filter(array $sources): array;
}
