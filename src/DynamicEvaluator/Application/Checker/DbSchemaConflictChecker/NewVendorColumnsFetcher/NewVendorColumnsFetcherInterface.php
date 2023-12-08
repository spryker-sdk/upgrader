<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\NewVendorColumnsFetcher;

interface NewVendorColumnsFetcherInterface
{
    /**
     * @return array<string, array<string>>
     */
    public function fetchUpdatedVendorColumnsMap(): array;
}
