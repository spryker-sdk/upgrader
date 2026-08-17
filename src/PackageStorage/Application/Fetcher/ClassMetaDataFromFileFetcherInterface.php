<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace PackageStorage\Application\Fetcher;

interface ClassMetaDataFromFileFetcherInterface
{
    public function fetchFQCN(string $filePath): ?string;

    public function fetchPackageName(string $filePath): ?string;
}
