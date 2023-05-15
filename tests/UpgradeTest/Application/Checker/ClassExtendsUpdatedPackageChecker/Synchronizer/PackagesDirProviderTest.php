<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer;

use PHPUnit\Framework\TestCase;
use Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesDirProvider;
use Upgrade\Infrastructure\IO\Filesystem;
use Upgrader\Configuration\ConfigurationProvider;

class PackagesDirProviderTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetSprykerPackageDirsShouldReturnFilteredDirs(): void
    {
        // Arrange
        $filesystemMock = $this->createFilesystemMock(['.', '..', 'spryker-sdk', 'spryker-shop', 'spryker', 'symfony']);
        $configurationProviderMock = $this->createConfigurationProviderMock('/data/');
        $packagesDirProvider = new PackagesDirProvider($configurationProviderMock, $filesystemMock);

        // Act
        $dirs = $packagesDirProvider->getSprykerPackageDirs();

        // Assert
        $this->assertSame(['spryker-sdk', 'spryker-shop', 'spryker'], $dirs);
    }

    /**
     * @return void
     */
    public function testGetFromDirShouldReturnFromDirPath(): void
    {
        // Arrange
        $filesystemMock = $this->createFilesystemMock(['.', '..', 'spryker-sdk', 'spryker-shop', 'spryker', 'symfony']);
        $configurationProviderMock = $this->createConfigurationProviderMock('/data/');
        $packagesDirProvider = new PackagesDirProvider($configurationProviderMock, $filesystemMock);

        // Act
        $dir = $packagesDirProvider->getFromDir();

        // Assert
        $this->assertSame($dir, '/data/' . PackagesDirProvider::FROM_DIR);
    }

    /**
     * @return void
     */
    public function testGetToDirShouldReturnToDirPath(): void
    {
        // Arrange
        $filesystemMock = $this->createFilesystemMock(['.', '..', 'spryker-sdk', 'spryker-shop', 'spryker', 'symfony']);
        $configurationProviderMock = $this->createConfigurationProviderMock('/data/');
        $packagesDirProvider = new PackagesDirProvider($configurationProviderMock, $filesystemMock);

        // Act
        $dir = $packagesDirProvider->getToDir();

        // Assert
        $this->assertSame($dir, '/data/' . PackagesDirProvider::TO_DIR);
    }

    /**
     * @param array<string> $dirFiles
     *
     * @return \Upgrade\Infrastructure\IO\Filesystem
     */
    public function createFilesystemMock(array $dirFiles): Filesystem
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('scanDir')->willReturn($dirFiles);

        return $filesystem;
    }

    /**
     * @param string $rootPath
     *
     * @return \Upgrader\Configuration\ConfigurationProvider
     */
    public function createConfigurationProviderMock(string $rootPath): ConfigurationProvider
    {
        $configurationProvider = $this->createMock(ConfigurationProvider::class);
        $configurationProvider->method('getRootPath')->willReturn($rootPath);

        return $configurationProvider;
    }
}
