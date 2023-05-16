<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher;

interface ClassMetaDataFromFileFetcherInterface
{
    /**
     * @param string $filePath
     *
     * @return string|null
     */
    public function fetchFQCN(string $filePath): ?string;

    /**
     * @param string $filePath
     *
     * @return string|null
     */
    public function fetchPackageName(string $filePath): ?string;
}
