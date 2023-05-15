<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher;

use PHPUnit\Framework\TestCase;
use Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\ClassMetaDataFromFileFetcher;
use Upgrade\Infrastructure\IO\Filesystem;

class ClassMetaDataFromFileFetcherTest extends TestCase
{
    /**
     * @dataProvider getFQCNProvider
     *
     * @param string $fileData
     * @param string|null $expectedClassName
     *
     * @return void
     */
    public function testFetchFQCNShouldReturnClassName(string $fileData, ?string $expectedClassName): void
    {
        // Arrange
        $classMetaDataFromFileFetcher = new ClassMetaDataFromFileFetcher($this->createFilesystemFacadeMock($fileData));

        // Act
        $className = $classMetaDataFromFileFetcher->fetchFQCN('someFile.php');

        // Assert
        $this->assertSame($expectedClassName, $className);
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function getFQCNProvider(): array
    {
        return [
            'simpleClass' => [
                <<<DATA
                namespace UpgradeTest\Application\Checker;

                use Upgrade\Infrastructure\IO\Filesystem;

                class ClassA
                {
                }
                DATA,
                'UpgradeTest\Application\Checker\ClassA',
            ],
            'withExtends' => [
                <<<DATA
                namespace UpgradeTest\Application\Checker;

                use Upgrade\Infrastructure\IO\Filesystem;

                class ClassA extends ClassB implements InterfaceC
                {
                }
                DATA,
                'UpgradeTest\Application\Checker\ClassA',
            ],
            'abstract' => [
                <<<DATA
                namespace UpgradeTest\Application\Checker;

                use Upgrade\Infrastructure\IO\Filesystem;

                abstract class ClassA extends ClassB
                {
                }
                DATA,
                'UpgradeTest\Application\Checker\ClassA',
            ],
            'caseWithGlobalNamespace' => [
                <<<DATA
                use Upgrade\Infrastructure\IO\Filesystem;

                class ClassA extends ClassB
                {
                }
                DATA,
                '\ClassA',
            ],
            'invalidCaseWithInterface' => [
                <<<DATA
                namespace UpgradeTest\Application\Checker;

                use Upgrade\Infrastructure\IO\Filesystem;

                interface ClassA extends ClassB
                {
                }
                DATA,
                null,
            ],
        ];
    }

    /**
     * @return void
     */
    public function testFetchPackageNameShouldReturnPackageName(): void
    {
        // Arrange

        $fileName = '/data/project/src/Spryker/Zed/Acl/Business/Model/GroupInterface.php';

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('exists')->willReturn(true);
        $filesystem->method('readFile')
            ->with($this->equalTo('/data/project/composer.json'))
            ->willReturn(
                <<<JSOM
                {
                    "name": "spryker/acl",
                    "type": "library",
                    "description": "Acl module",
                    "license": "proprietary",
                    "require": {}
                }
                JSOM,
            );

        $classMetaDataFromFileFetcher = new ClassMetaDataFromFileFetcher($filesystem);

        // Act
        $packageName = $classMetaDataFromFileFetcher->fetchPackageName($fileName);

        // Assert
        $this->assertSame('spryker/acl', $packageName);
    }

    /**
     * @return void
     */
    public function testFetchPackageNameShouldReturnNullWithInvalidPath(): void
    {
        // Arrange

        $fileName = '/data/project/test/Spryker/Zed/Acl/Business/Model/GroupInterface.php';

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('exists')->willReturn(true);
        $filesystem->method('readFile')
            ->with($this->equalTo('/data/project/composer.json'))
            ->willReturn(
                <<<JSOM
                {
                    "name": "spryker/acl",
                    "type": "library",
                    "description": "Acl module",
                    "license": "proprietary",
                    "require": {}
                }
                JSOM,
            );

        $classMetaDataFromFileFetcher = new ClassMetaDataFromFileFetcher($filesystem);

        // Act
        $packageName = $classMetaDataFromFileFetcher->fetchPackageName($fileName);

        // Assert
        $this->assertNull($packageName);
    }

    /**
     * @return void
     */
    public function testFetchPackageNameShouldReturnNullWhenComposerFileDoesNotExist(): void
    {
        // Arrange

        $fileName = '/data/project/test/Spryker/Zed/Acl/Business/Model/GroupInterface.php';

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('exists')->willReturn(false);

        $classMetaDataFromFileFetcher = new ClassMetaDataFromFileFetcher($filesystem);

        // Act
        $packageName = $classMetaDataFromFileFetcher->fetchPackageName($fileName);

        // Assert
        $this->assertNull($packageName);
    }

    /**
     * @param string $fileContent
     *
     * @return \Upgrade\Infrastructure\IO\Filesystem
     */
    public function createFilesystemFacadeMock(string $fileContent): Filesystem
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('readFile')->willReturn($fileContent);

        return $filesystem;
    }
}
