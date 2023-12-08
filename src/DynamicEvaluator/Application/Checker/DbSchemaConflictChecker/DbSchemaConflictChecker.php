<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\DbSchemaConflictChecker;

use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\Dto\ViolationDto;
use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\NewVendorColumnsFetcher\NewVendorColumnsFetcherInterface;
use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\ProjectSchemaColumnsMapFetcher\ProjectSchemaColumnsMapFetcherInterface;

class DbSchemaConflictChecker
{
    /**
     * @var \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\ProjectSchemaColumnsMapFetcher\ProjectSchemaColumnsMapFetcherInterface
     */
    protected ProjectSchemaColumnsMapFetcherInterface $projectSchemaColumnsMapFetcher;

    /**
     * @var \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\NewVendorColumnsFetcher\NewVendorColumnsFetcherInterface
     */
    protected NewVendorColumnsFetcherInterface $newVendorColumnsFetcher;

    /**
     * @param \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\ProjectSchemaColumnsMapFetcher\ProjectSchemaColumnsMapFetcherInterface $projectSchemaColumnsMapFetcher
     * @param \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\NewVendorColumnsFetcher\NewVendorColumnsFetcherInterface $newVendorColumnsFetcher
     */
    public function __construct(
        ProjectSchemaColumnsMapFetcherInterface $projectSchemaColumnsMapFetcher,
        NewVendorColumnsFetcherInterface $newVendorColumnsFetcher
    ) {
        $this->projectSchemaColumnsMapFetcher = $projectSchemaColumnsMapFetcher;
        $this->newVendorColumnsFetcher = $newVendorColumnsFetcher;
    }

    /**
     * @return array<\DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\Dto\ViolationDto>
     */
    public function check(): array
    {
        $currentProjectColumnsMap = $this->projectSchemaColumnsMapFetcher->fetcherColumnsMap();
        $newVendorColumnsMap = $this->newVendorColumnsFetcher->fetchUpdatedVendorColumnsMap();

        $violations = [];

        foreach ($currentProjectColumnsMap as $file => $projectColumnsMap) {
            $violations[] = $this->getProjectFileViolations($projectColumnsMap, $newVendorColumnsMap, $file);
        }

        return array_merge(...$violations);
    }

    /**
     * @param array<string, array<string>> $projectColumnsMap
     * @param array<string, array<string>> $newVendorColumnsMap
     * @param string $projectFile
     *
     * @return array<\DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\Dto\ViolationDto>
     */
    protected function getProjectFileViolations(array $projectColumnsMap, array $newVendorColumnsMap, string $projectFile): array
    {
        $violations = [];

        foreach ($projectColumnsMap as $table => $columns) {
            if (!isset($newVendorColumnsMap[$table])) {
                continue;
            }

            $commonColumns = array_values(array_intersect($columns, $newVendorColumnsMap[$table]));

            if (count($commonColumns) === 0) {
                continue;
            }

            $violations[] = new ViolationDto($projectFile, $table, $commonColumns);
        }

        return $violations;
    }
}
