<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\VendorChangedFilesFetcher;
use Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesDirProvider;
use Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesDirProviderInterface;
use Upgrade\Infrastructure\IO\ProcessFactory;

class VendorChangedFilesFetcherTest extends TestCase
{
    /**
     * @return void
     */
    public function testFetchChangedFilesShouldReturnVendorFiles(): void
    {
        // Arrange
        $packagesDirProviderMock = $this->createPackagesDirProviderMock(['spryker']);
        $processFactoryMock = $this->createProcessFactoryMock(
            <<<OUT
            src/Spryker/Zed/Acl/Business/Model/Group.php
            src/Spryker/Zed/Acl/Business/Model/Installer.php
            OUT,
        );

        $vendorChangedFilesFetcher = new VendorChangedFilesFetcher($packagesDirProviderMock, $processFactoryMock);

        // Act
        $files = $vendorChangedFilesFetcher->fetchChangedFiles();

        // Assert
        $this->assertSame([
            PackagesDirProvider::TO_DIR . 'spryker/src/Spryker/Zed/Acl/Business/Model/Group.php',
            PackagesDirProvider::TO_DIR . 'spryker/src/Spryker/Zed/Acl/Business/Model/Installer.php',
        ], $files);
    }

    /**
     * @return void
     */
    public function testFetchChangedFilesShouldEmptyArrayWhenCommandOutputEmpty(): void
    {
        // Arrange
        $packagesDirProviderMock = $this->createPackagesDirProviderMock(['spryker']);
        $processFactoryMock = $this->createProcessFactoryMock('');

        $vendorChangedFilesFetcher = new VendorChangedFilesFetcher($packagesDirProviderMock, $processFactoryMock);

        // Act
        $files = $vendorChangedFilesFetcher->fetchChangedFiles();

        // Assert
        $this->assertEmpty($files);
    }

    /**
     * @param array<string> $dirs
     *
     * @return \Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesDirProviderInterface
     */
    public function createPackagesDirProviderMock(array $dirs): PackagesDirProviderInterface
    {
        $packagesDirProvider = $this->createMock(PackagesDirProviderInterface::class);
        $packagesDirProvider->method('getSprykerPackageDirs')->willReturn($dirs);
        $packagesDirProvider->method('getFromDir')->willReturn(PackagesDirProvider::FROM_DIR);
        $packagesDirProvider->method('getToDir')->willReturn(PackagesDirProvider::TO_DIR);

        return $packagesDirProvider;
    }

    /**
     * @param string $output
     *
     * @return \Upgrade\Infrastructure\IO\ProcessFactory
     */
    public function createProcessFactoryMock(string $output): ProcessFactory
    {
        $process = $this->createMock(Process::class);
        $process->method('getOutput')->willReturn($output);

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory->method('createFromShellCommandline')->willReturn($process);

        return $processFactory;
    }
}
