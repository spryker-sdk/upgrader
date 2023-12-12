<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace PackageStorage\Application\Fetcher;

interface VendorChangedFilesFetcherInterface
{
    /**
     * @return array<string> List of diff files
     */
    public function fetchChangedFiles(): array;
}
