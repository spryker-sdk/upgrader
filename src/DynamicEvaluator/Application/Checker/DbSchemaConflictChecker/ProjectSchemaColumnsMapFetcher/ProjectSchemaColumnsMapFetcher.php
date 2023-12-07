<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\ProjectSchemaColumnsMapFetcher;

use Core\Infrastructure\Service\FinderFactory;
use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\XmlSchemaFileParser\XmlSchemaFileParserInterface;
use Upgrader\Configuration\ConfigurationProvider;

class ProjectSchemaColumnsMapFetcher implements ProjectSchemaColumnsMapFetcherInterface
{
    /**
     * @var array<string>
     */
    protected const EXCLUDED_DIRS = ['Generated', 'Orm'];

    /**
     * @var \Core\Infrastructure\Service\FinderFactory
     */
    protected FinderFactory $finderFactory;

    /**
     * @var \Upgrader\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\XmlSchemaFileParser\XmlSchemaFileParserInterface
     */
    protected XmlSchemaFileParserInterface $xmlSchemaFileParser;

    /**
     * @param \Core\Infrastructure\Service\FinderFactory $finderFactory
     * @param \Upgrader\Configuration\ConfigurationProvider $configurationProvider
     * @param \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\XmlSchemaFileParser\XmlSchemaFileParserInterface $xmlSchemaFileParser
     */
    public function __construct(
        FinderFactory $finderFactory,
        ConfigurationProvider $configurationProvider,
        XmlSchemaFileParserInterface $xmlSchemaFileParser
    ) {
        $this->finderFactory = $finderFactory;
        $this->configurationProvider = $configurationProvider;
        $this->xmlSchemaFileParser = $xmlSchemaFileParser;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function fetcherColumnsMap(): array
    {
        $projectXmlSchemaData = [];

        $finder = $this->finderFactory->createFinder();
        $finder->name('*.schema.xml');

        $fileIterator = $finder->in($this->configurationProvider->getSrcPath())->exclude(static::EXCLUDED_DIRS);

        foreach ($fileIterator as $file) {
            $filePath = $file->getRealPath();

            $columnsHashMap = $this->xmlSchemaFileParser->parseXmlToColumnsMap($filePath);

            if (count($columnsHashMap) === 0) {
                continue;
            }

            $projectXmlSchemaData[$this->getProjectRelativePath($filePath)] = $columnsHashMap;
        }

        return $projectXmlSchemaData;
    }

    /**
     * @param string $absolutePath
     *
     * @return string
     */
    protected function getProjectRelativePath(string $absolutePath): string
    {
        return (string)preg_replace('/.*(\/src\/.*)/', '$1', $absolutePath);
    }
}
