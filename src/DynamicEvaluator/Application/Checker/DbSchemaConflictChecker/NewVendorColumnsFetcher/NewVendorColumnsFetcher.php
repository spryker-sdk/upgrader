<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\NewVendorColumnsFetcher;

use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\XmlSchemaFileParser\XmlSchemaFileParserInterface;
use DynamicEvaluator\Application\PackagesSynchronizer\PackagesDirProviderInterface;

class NewVendorColumnsFetcher implements NewVendorColumnsFetcherInterface
{
    /**
     * @var \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\NewVendorColumnsFetcher\ChangedXmlFilesFetcher
     */
    protected ChangedXmlFilesFetcher $changedXmlFilesFetcher;

    /**
     * @var \DynamicEvaluator\Application\PackagesSynchronizer\PackagesDirProviderInterface
     */
    protected PackagesDirProviderInterface $packagesDirProvider;

    /**
     * @var \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\XmlSchemaFileParser\XmlSchemaFileParserInterface
     */
    protected XmlSchemaFileParserInterface $xmlSchemaFileParser;

    /**
     * @param \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\NewVendorColumnsFetcher\ChangedXmlFilesFetcher $changedXmlFilesFetcher
     * @param \DynamicEvaluator\Application\PackagesSynchronizer\PackagesDirProviderInterface $packagesDirProvider
     * @param \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\XmlSchemaFileParser\XmlSchemaFileParserInterface $xmlSchemaFileParser
     */
    public function __construct(
        ChangedXmlFilesFetcher $changedXmlFilesFetcher,
        PackagesDirProviderInterface $packagesDirProvider,
        XmlSchemaFileParserInterface $xmlSchemaFileParser
    ) {
        $this->changedXmlFilesFetcher = $changedXmlFilesFetcher;
        $this->packagesDirProvider = $packagesDirProvider;
        $this->xmlSchemaFileParser = $xmlSchemaFileParser;
    }

    /**
     * @return array<string, array<string>>
     */
    public function fetchUpdatedVendorColumnsMap(): array
    {
        $changesSchemaFiles = $this->changedXmlFilesFetcher->fetchChangedXmlSchemaFiles(
            $this->packagesDirProvider->getFromDir(),
            $this->packagesDirProvider->getToDir(),
        );

        $currentColumnsState = [];
        $previousColumnsState = [];

        foreach ($changesSchemaFiles as $changesSchemaFile) {
            $currentVendorFile = $this->packagesDirProvider->getFromDir() . ltrim(
                $changesSchemaFile,
                DIRECTORY_SEPARATOR,
            );

            $previousVendorFile = $this->packagesDirProvider->getToDir() . ltrim(
                $changesSchemaFile,
                DIRECTORY_SEPARATOR,
            );

            $currentColumnsState = $this->mergeColumnsMaps(
                $currentColumnsState,
                $this->xmlSchemaFileParser->parseXmlToColumnsMap($currentVendorFile),
            );

            $previousColumnsState = $this->mergeColumnsMaps(
                $previousColumnsState,
                $this->xmlSchemaFileParser->parseXmlToColumnsMap($previousVendorFile),
            );
        }

        return $this->getColumnsDiff($previousColumnsState, $currentColumnsState);
    }

    /**
     * @param array<string, array<string>> $columnsMapOne
     * @param array<string, array<string>> $columnsMapTwo
     *
     * @return array<string, array<string>>
     */
    protected function mergeColumnsMaps(array $columnsMapOne, array $columnsMapTwo): array
    {
        $mergedColumnMap = array_merge($columnsMapOne, $columnsMapTwo);

        foreach ($mergedColumnMap as $table => $columns) {
            if (isset($columnsMapOne[$table])) {
                $mergedColumnMap[$table] = array_unique(array_merge($columns, $columnsMapOne[$table]));
            }
        }

        return $mergedColumnMap;
    }

    /**
     * @param array<string, array<string>> $previousColumnsState
     * @param array<string, array<string>> $currentColumnsState
     *
     * @return array<string, array<string>>
     */
    protected function getColumnsDiff(array $previousColumnsState, array $currentColumnsState): array
    {
        $newColumnsMap = [];

        foreach ($currentColumnsState as $table => $columns) {
            if (!isset($previousColumnsState[$table])) {
                $newColumnsMap[$table] = $columns;

                continue;
            }

            $newColumns = array_values(array_diff($columns, $previousColumnsState[$table]));

            if (count($newColumns) === 0) {
                continue;
            }

            $newColumnsMap[$table] = $newColumns;
        }

        return $newColumnsMap;
    }
}
