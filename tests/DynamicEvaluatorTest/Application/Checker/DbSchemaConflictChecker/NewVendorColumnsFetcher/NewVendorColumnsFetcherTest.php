<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\DbSchemaConflictChecker\NewVendorColumnsFetcher;

use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\NewVendorColumnsFetcher\ChangedXmlFilesFetcher;
use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\NewVendorColumnsFetcher\NewVendorColumnsFetcher;
use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\XmlSchemaFileParser\XmlSchemaFileParserInterface;
use PackageStorage\Application\PackagesSynchronizer\PackagesDirProviderInterface;
use PHPUnit\Framework\TestCase;

class NewVendorColumnsFetcherTest extends TestCase
{
    /**
     * @return void
     */
    public function testFetchUpdatedVendorColumnsMapShouldCalculateColumnsMap(): void
    {
        // Arrange
        $newVendorsColumnsFetcher = new NewVendorColumnsFetcher(
            $this->createChangedXmlFilesFetcherMock(),
            $this->createPackagesDirProviderMock(),
            $this->createXmlSchemaFileParserMock(
                [
                    ['table_one' => ['col_one', 'col_two', 'col_three'], 'table_new' => ['col_one'], 'table_same' => ['col_one']],
                    ['table_one' => ['col_one', 'col_four'], 'table_same' => ['col_one']],
                    ['table_two' => ['col_one', 'col_two'], 'table_one' => ['col_two']],
                    ['table_two' => ['col_two'], 'table_one' => ['col_two']],
                ],
            ),
        );

        // Act
        $result = $newVendorsColumnsFetcher->fetchUpdatedVendorColumnsMap();

        // Assert
        $this->assertSame(['table_one' => ['col_three'], 'table_new' => ['col_one'], 'table_two' => ['col_one']], $result);
    }

    /**
     * @return \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\NewVendorColumnsFetcher\ChangedXmlFilesFetcher
     */
    protected function createChangedXmlFilesFetcherMock(): ChangedXmlFilesFetcher
    {
        $changedXmlFilesFetcher = $this->createMock(ChangedXmlFilesFetcher::class);
        $changedXmlFilesFetcher->method('fetchChangedXmlSchemaFiles')->willReturn([
            'src/Spryker/Zed/ApiKey/Persistence/Propel/Schema/spy_api_key.schema.xml',
            'src/Spryker/Zed/Acl/Persistence/Propel/Schema/spy_acl_key.schema.xml',
        ]);

        return $changedXmlFilesFetcher;
    }

    /**
     * @return \PackageStorage\Application\PackagesSynchronizer\PackagesDirProviderInterface
     */
    protected function createPackagesDirProviderMock(): PackagesDirProviderInterface
    {
        $packagesDirProvider = $this->createMock(PackagesDirProviderInterface::class);
        $packagesDirProvider->method('getToDir')->willReturn('previous');
        $packagesDirProvider->method('getFromDir')->willReturn('current');

        return $packagesDirProvider;
    }

    /**
     * @param array<mixed> $consecutiveReturns
     *
     * @return \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\XmlSchemaFileParser\XmlSchemaFileParserInterface
     */
    protected function createXmlSchemaFileParserMock(array $consecutiveReturns): XmlSchemaFileParserInterface
    {
        $xmlSchemaFileParser = $this->createMock(XmlSchemaFileParserInterface::class);
        $xmlSchemaFileParser->method('parseXmlToColumnsMap')->will($this->onConsecutiveCalls(...$consecutiveReturns));

        return $xmlSchemaFileParser;
    }
}
