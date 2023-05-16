<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher;

interface VendorChangedClassesFetcherInterface
{
    /**
     * @return array<string, string> Key - className. Value - packageName
     */
    public function fetchVendorChangedClassesWithPackage(): array;
}
