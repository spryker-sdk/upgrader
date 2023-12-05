<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\DbSchemaConflictChecker;

use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\DbSchemaConflictChecker;
use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\NewVendorColumnsFetcher\NewVendorColumnsFetcherInterface;
use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\ProjectSchemaColumnsMapFetcher\ProjectSchemaColumnsMapFetcherInterface;
use PHPUnit\Framework\TestCase;

class DbSchemaConflictCheckerTest extends TestCase
{
    /**
     * @return void
     */
    public function testCheckShouldReturnViolationsWithColumnsAlreadyInProject(): void
    {
        // Arrange
        $fileName = 'src/Spryker/Zed/ApiKey/Persistence/Propel/Schema/spy_api_key.schema.xml';

        $columnsInProject = [
            $fileName => [
                'api_key' => ['col_one', 'col_two'],
                'api_token' => ['col_one'],
                'custom_table' => ['col_one'],
            ],
        ];

        $newVendorColumns = [
            'api_key' => ['col_two', 'col_three'],
            'api_token' => ['col_two'],
            'acl' => ['col_one', 'col_two'],
        ];

        $dbSchemaConflictChecker = new DbSchemaConflictChecker(
            $this->createProjectSchemaColumnsMapFetcherMock($columnsInProject),
            $this->createNewVendorColumnsFetcherMock($newVendorColumns),
        );

        // Act
        $violations = $dbSchemaConflictChecker->check();

        // Assert
        $this->assertCount(1, $violations);
        $violations = $violations[0];

        $this->assertSame($fileName, $violations->getProjectFile());
        $this->assertSame('api_key', $violations->getTable());
        $this->assertSame(['col_two'], $violations->getColumns());
    }

    /**
     * @param array<mixed> $projectColumnsMap
     *
     * @return \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\ProjectSchemaColumnsMapFetcher\ProjectSchemaColumnsMapFetcherInterface
     */
    protected function createProjectSchemaColumnsMapFetcherMock(array $projectColumnsMap = []): ProjectSchemaColumnsMapFetcherInterface
    {
        $projectSchemaColumnsMapFetcher = $this->createMock(ProjectSchemaColumnsMapFetcherInterface::class);
        $projectSchemaColumnsMapFetcher->method('fetcherColumnsMap')->willReturn($projectColumnsMap);

        return $projectSchemaColumnsMapFetcher;
    }

    /**
     * @param array<mixed> $newVendorColumns
     *
     * @return \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\NewVendorColumnsFetcher\NewVendorColumnsFetcherInterface
     */
    protected function createNewVendorColumnsFetcherMock(array $newVendorColumns): NewVendorColumnsFetcherInterface
    {
        $newVendorColumnsFetcher = $this->createMock(NewVendorColumnsFetcherInterface::class);
        $newVendorColumnsFetcher->method('fetchUpdatedVendorColumnsMap')->willReturn($newVendorColumns);

        return $newVendorColumnsFetcher;
    }
}
