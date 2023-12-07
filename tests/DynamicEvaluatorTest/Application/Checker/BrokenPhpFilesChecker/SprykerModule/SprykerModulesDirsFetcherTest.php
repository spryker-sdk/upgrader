<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\BrokenPhpFilesChecker\SprykerModule;

use ArrayObject;
use Core\Infrastructure\Service\FinderFactory;
use DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule\SprykerModulesDirsFetcher;
use DynamicEvaluator\Application\ProjectConfigReader\ProjectConfigReaderInterface;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Upgrader\Configuration\ConfigurationProvider;

class SprykerModulesDirsFetcherTest extends TestCase
{
    /**
     * @return void
     */
    public function testFetchModulesDirsShouldReturnValidDirectories(): void
    {
        // Arrange
        $projectConfigReaderMock = $this->createProjectConfigReaderMock();
        $configurationProviderMock = $this->createConfigurationProviderMock();
        $finderFactoryMock = $this->createFinderFactoryMock(['/data/src/Pyz/Zed/ModuleOne', '/data/src/Pyz/Yves/ModuleOne'], '/^ModuleOne$/');
        $sprykerModulesDirsFetcher = new SprykerModulesDirsFetcher($projectConfigReaderMock, $configurationProviderMock, $finderFactoryMock);

        // Act
        $dirs = $sprykerModulesDirsFetcher->fetchModulesDirs(['spryker/module-one']);

        // Assert
        $this->assertSame(['/data/src/Pyz/Zed/ModuleOne', '/data/src/Pyz/Yves/ModuleOne'], $dirs);
    }

    /**
     * @return \DynamicEvaluator\Application\ProjectConfigReader\ProjectConfigReaderInterface
     */
    protected function createProjectConfigReaderMock(): ProjectConfigReaderInterface
    {
        $projectConfigReader = $this->createMock(ProjectConfigReaderInterface::class);
        $projectConfigReader->method('getProjectNamespaces')->willReturn(['Pyz']);

        return $projectConfigReader;
    }

    /**
     * @return \Upgrader\Configuration\ConfigurationProvider
     */
    protected function createConfigurationProviderMock(): ConfigurationProvider
    {
        $configurationProvider = $this->createMock(ConfigurationProvider::class);
        $configurationProvider->method('getRootPath')->willReturn('/data/');

        return $configurationProvider;
    }

    /**
     * @param array<string> $paths
     * @param string $expectedRegExp
     *
     * @return \Core\Infrastructure\Service\FinderFactory
     */
    protected function createFinderFactoryMock(array $paths, string $expectedRegExp): FinderFactory
    {
        $paths = array_map(function (string $path): SplFileInfo {
            $fi = $this->createMock(SplFileInfo::class);
            $fi->method('getPathname')->willReturn($path);

            return $fi;
        }, $paths);

        $finder = $this->createMock(Finder::class);

        $finder->expects($this->once())->method('depth')->willReturn($finder);
        $finder->expects($this->once())->method('path')->with($expectedRegExp)->willReturn($finder);
        $finder->expects($this->once())->method('directories')->willReturn($finder);
        $finder->method('in')->willReturn(new ArrayObject($paths));

        $finderFactory = $this->createMock(FinderFactory::class);
        $finderFactory->method('createFinder')->willReturn($finder);

        return $finderFactory;
    }
}
