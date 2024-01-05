<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\ModuleNameConflictChecker\Fetcher;

use ArrayIterator;
use Core\Infrastructure\Service\FinderFactory;
use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Fetcher\ProjectModulesNamesFetcher;
use DynamicEvaluator\Application\ProjectConfigReader\ProjectConfigReaderInterface;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Upgrader\Configuration\ConfigurationProvider;

class ProjectModulesNamesFetcherTest extends TestCase
{
    /**
     * @return void
     */
    public function testFetchProjectModulesShouldReturnProjectModules(): void
    {
        // Arrange
        $projectConfigReaderMock = $this->createProjectConfigReaderMock(['Zxc']);
        $finderFactoryMock = $this->createFinderFactoryMock(
            array_map(static fn (string $layer): string => sprintf('/data/src/Zxc/%s', $layer), ProjectModulesNamesFetcher::MODULE_LAYERS),
            ['Acl', 'Quote'],
        );
        $configurationProviderMock = $this->createConfigurationProviderMock('/data/');

        $projectModulesNamesFetcher = new ProjectModulesNamesFetcher(
            $projectConfigReaderMock,
            $finderFactoryMock,
            $configurationProviderMock,
        );

        // Act
        $modules = $projectModulesNamesFetcher->fetchProjectModules();

        // Assert
        $this->assertSame(['Acl', 'Quote'], $modules);
    }

    /**
     * @param array<string> $projectNameSpaces
     *
     * @return \DynamicEvaluator\Application\ProjectConfigReader\ProjectConfigReaderInterface
     */
    protected function createProjectConfigReaderMock(array $projectNameSpaces): ProjectConfigReaderInterface
    {
        $projectConfigReader = $this->createMock(ProjectConfigReaderInterface::class);
        $projectConfigReader->method('getProjectNamespaces')->willReturn($projectNameSpaces);

        return $projectConfigReader;
    }

    /**
     * @param array<string> $expectedLookupPaths
     * @param array<string> $modules
     *
     * @return \Core\Infrastructure\Service\FinderFactory
     */
    protected function createFinderFactoryMock(array $expectedLookupPaths, array $modules): FinderFactory
    {
        $modules = array_map(static fn (string $dir): \SplFileInfo => new SplFileInfo($dir), $modules);

        $finderMock = $this->createMock(Finder::class);
        $finderMock->method('directories')->willReturn($finderMock);
        $finderMock->method('in')->willReturn($finderMock);
        $finderMock->expects($this->once())->method('in')->with($expectedLookupPaths)->willReturn($finderMock);
        $finderMock->method('getIterator')->willReturn(new ArrayIterator($modules));

        $finderFactory = $this->createMock(FinderFactory::class);
        $finderFactory->method('createFinder')->willReturn($finderMock);

        return $finderFactory;
    }

    /**
     * @param string $rootPath
     *
     * @return \Upgrader\Configuration\ConfigurationProvider
     */
    protected function createConfigurationProviderMock(string $rootPath): ConfigurationProvider
    {
        $configurationProvider = $this->createMock(ConfigurationProvider::class);
        $configurationProvider->method('getRootPath')->willReturn($rootPath);

        return $configurationProvider;
    }
}
