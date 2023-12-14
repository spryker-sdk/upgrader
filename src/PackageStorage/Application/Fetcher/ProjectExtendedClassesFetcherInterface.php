<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace PackageStorage\Application\Fetcher;

interface ProjectExtendedClassesFetcherInterface
{
    /**
     * @return array<string, string> Key - FQCN; value - file path
     */
    public function fetchExtendedClasses(): array;
}
