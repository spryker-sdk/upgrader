<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\DbSchemaConflictChecker\ProjectSchemaColumnsMapFetcher;

use ArrayObject;
use Core\Infrastructure\Service\FinderFactory;
use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\ProjectSchemaColumnsMapFetcher\ProjectSchemaColumnsMapFetcher;
use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\XmlSchemaFileParser\XmlSchemaFileParserInterface;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Upgrader\Configuration\ConfigurationProvider;

class ProjectSchemaColumnsMapFetcherTest extends TestCase
{
    /**
     * @return void
     */
    public function testFetcherColumnsMapShouldReturnProjectColumnsMap(): void
    {
        // Arrange
        $projectSchemaColumnsMapFetcher = new ProjectSchemaColumnsMapFetcher(
            $this->createFinderFactoryMock([
                '/data/project/src/Spryker/Zed/ApiKey/Persistence/Propel/Schema/spy_acl.schema.xml',
                '/data/project/src/Spryker/Zed/ApiKey/Persistence/Propel/Schema/spy_api_key.schema.xml',
            ]),
            $this->createConfigurationProviderMock(),
            $this->createXmlSchemaFileParserMock([[], ['spy_api_key' => ['col_one', 'col_two']]]),
        );

        // Act
        $result = $projectSchemaColumnsMapFetcher->fetcherColumnsMap();

        // Assert
        $this->assertSame(['/src/Spryker/Zed/ApiKey/Persistence/Propel/Schema/spy_api_key.schema.xml' => ['spy_api_key' => ['col_one', 'col_two']]], $result);
    }

    /**
     * @param array<string> $files
     *
     * @return \Core\Infrastructure\Service\FinderFactory
     */
    protected function createFinderFactoryMock(array $files = []): FinderFactory
    {
        $files = array_map(function (string $path): SplFileInfo {
            $fileInfo = $this->createMock(SplFileInfo::class);
            $fileInfo->method('getRealPath')->willReturn($path);

            return $fileInfo;
        }, $files);

        $finder = $this->createMock(Finder::class);
        $finder->method('name')->willReturn($finder);
        $finder->method('in')->willReturn($finder);
        $finder->method('exclude')->willReturn(new ArrayObject($files));

        $finderFactory = $this->createMock(FinderFactory::class);
        $finderFactory->method('createFinder')->willReturn($finder);

        return $finderFactory;
    }

    /**
     * @return \Upgrader\Configuration\ConfigurationProvider
     */
    protected function createConfigurationProviderMock(): ConfigurationProvider
    {
        $configurationProvider = $this->createMock(ConfigurationProvider::class);
        $configurationProvider->method('getSrcPath')->willReturn('');

        return $configurationProvider;
    }

    /**
     * @param array<mixed> $returnValues
     *
     * @return \DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\XmlSchemaFileParser\XmlSchemaFileParserInterface
     */
    protected function createXmlSchemaFileParserMock(array $returnValues = []): XmlSchemaFileParserInterface
    {
        $xmlSchemaFileParser = $this->createMock(XmlSchemaFileParserInterface::class);
        $xmlSchemaFileParser->method('parseXmlToColumnsMap')->will($this->onConsecutiveCalls(...$returnValues));

        return $xmlSchemaFileParser;
    }
}
