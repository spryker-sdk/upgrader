<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\ProjectSchemaColumnsMapFetcher;

interface ProjectSchemaColumnsMapFetcherInterface
{
    /**
     * @return array<string, array<mixed>>
     */
    public function fetcherColumnsMap(): array;
}
