<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\DbSchemaConflictChecker\ProjectSchemaColumnsMapFetcher;

use ArrayIterator;
use Core\Infrastructure\Service\FinderFactory;
use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\ProjectSchemaColumnsMapFetcher\ProjectSchemaColumnsMapFetcher;
use DynamicEvaluator\Application\Checker\DbSchemaConflictChecker\XmlSchemaFileParser\XmlSchemaFileParserInterface;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Upgrader\Configuration\ConfigurationProvider;

class ProjectSchemaColumnsMapFetcherTest extends TestCase
{
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
        $finder->method('exclude')->willReturn($finder);
        $finder->expects($this->once())->method('in')->willReturn($finder);
        $finder->method('getIterator')->willReturn(new ArrayIterator($files));

        $finderFactory = $this->createMock(FinderFactory::class);
        $finderFactory->method('createFinder')->willReturn($finder);

        return $finderFactory;
    }

    protected function createConfigurationProviderMock(): ConfigurationProvider
    {
        $configurationProvider = $this->createMock(ConfigurationProvider::class);
        $configurationProvider->method('getSrcPath')->willReturn('');

        return $configurationProvider;
    }

    /**
     * @param array<mixed> $returnValues
     */
    protected function createXmlSchemaFileParserMock(array $returnValues = []): XmlSchemaFileParserInterface
    {
        $xmlSchemaFileParser = $this->createMock(XmlSchemaFileParserInterface::class);
        $xmlSchemaFileParser->method('parseXmlToColumnsMap')->willReturnOnConsecutiveCalls(...$returnValues);

        return $xmlSchemaFileParser;
    }
}
