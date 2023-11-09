<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher;

use ArrayIterator;
use Core\Infrastructure\Service\FinderFactory;
use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\ProjectExtendedClassesFetcher;
use DynamicEvaluator\Application\PublicApiFilePathsProvider\PublicApiFilePathsProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Upgrader\Configuration\ConfigurationProvider;

class ProjectExtendedClassesFetcherTest extends TestCase
{
    /**
     * @dataProvider getExtendedClassesDataProvider
     *
     * @param string $classContent
     * @param string|null $expectedClass
     *
     * @return void
     */
    public function testFetchExtendedClassesShouldFetchExtendedClasses(string $classContent, ?string $expectedClass): void
    {
        // Arrange
        $filePath = '/data/project/ClassA.php';
        $finderMock = $this->createFinderMock($classContent, $filePath);

        $finderFactoryMock = $this->createMock(FinderFactory::class);
        $finderFactoryMock->method('createFinder')->willReturn($finderMock);

        $configurationProvider = $this->createMock(ConfigurationProvider::class);
        $publicApiFilePathsProvider = $this->createMock(PublicApiFilePathsProviderInterface::class);

        $projectExtendedClassesFetcher = new ProjectExtendedClassesFetcher($configurationProvider, $finderFactoryMock, $publicApiFilePathsProvider);

        // Act
        $extendedClass = $projectExtendedClassesFetcher->fetchExtendedClasses();

        // Assert
        $expectedClass !== null
            ? $this->assertSame([$expectedClass => $filePath], $extendedClass)
            : $this->assertSame([], $extendedClass);
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function getExtendedClassesDataProvider(): array
    {
        return [
            'extendedClass' => [
                <<<CLASS
                namespace Checker;

                use UpgradeTest\Application\Checker;

                class ClassA extends Checker
                {}
                CLASS,
                'UpgradeTest\Application\Checker',
            ],
            'extendedWithAliasClass' => [
                <<<CLASS
                namespace Checker;

                use UpgradeTest\Application\Checker as CheckerAlias;

                class ClassA extends CheckerAlias
                {}
                CLASS,
                'UpgradeTest\Application\Checker',
            ],
            'noExtendedWithAliasClass' => [
                <<<CLASS
                namespace Checker;

                class ClassA
                {}
                CLASS,
                null,
            ],
            'sameNameSpaceExtendedClass' => [
                <<<CLASS
                namespace Checker;

                class ClassA extends ClassB
                {}
                CLASS,
                null,
            ],
        ];
    }

    /**
     * @param string $classContent
     * @param string $filePath
     *
     * @return \Symfony\Component\Finder\Finder
     */
    public function createFinderMock(string $classContent, string $filePath): Finder
    {
        $fileInfo = $this->createMock(SplFileInfo::class);
        $fileInfo->method('getContents')->willReturn($classContent);
        $fileInfo->method('getRealPath')->willReturn($filePath);

        $finder = $this->createMock(Finder::class);
        $finder->method('name')->willReturn($finder);
        $finder->method('files')->willReturn($finder);
        $finder->method('notName')->willReturn($finder);
        $finder->method('in')->willReturn($finder);
        $finder->method('exclude')->willReturn($finder);
        $finder->method('getIterator')->willReturn(new ArrayIterator([$fileInfo]));

        return $finder;
    }
}
