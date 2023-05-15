<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesDirProvider;
use Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesDirProviderInterface;
use Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesSynchronizer;
use Upgrade\Infrastructure\IO\Filesystem;
use Upgrade\Infrastructure\IO\ProcessFactory;

class PackagesSynchronizerTest extends TestCase
{
    /**
     * @return void
     */
    public function testSyncShouldCreateDestinationDirIfNotExists(): void
    {
        // Arrange
        $fromDir = DIRECTORY_SEPARATOR . PackagesDirProvider::FROM_DIR;
        $toDir = DIRECTORY_SEPARATOR . PackagesDirProvider::TO_DIR;
        $packagesDirProvider = $this->createPackagesDirProviderMock(['spryker'], $fromDir, $toDir);

        $process = $this->createMock(Process::class);
        $process->expects($this->once())->method('mustRun');
        $processFactoryMock = $this->createProcessFactoryMock($process);

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())->method('exists')->with($toDir)->willReturn(false);
        $filesystem->expects($this->once())->method('mkdir')->with($toDir);

        $packagesSynchronizer = new PackagesSynchronizer($packagesDirProvider, $filesystem, $processFactoryMock);

        // Act
        $packagesSynchronizer->sync();
    }

    /**
     * @return void
     */
    public function testSyncShouldClearDataAndPassExceptionWhenExceptionThrown(): void
    {
        // Arrange
        $this->expectException(ProcessFailedException::class);

        $fromDir = DIRECTORY_SEPARATOR . PackagesDirProvider::FROM_DIR;
        $toDir = DIRECTORY_SEPARATOR . PackagesDirProvider::TO_DIR;
        $packagesDirProvider = $this->createPackagesDirProviderMock(['spryker'], $fromDir, $toDir);

        $process = $this->createMock(Process::class);
        $process->expects($this->once())->method('mustRun')->willThrowException(new ProcessFailedException($process));
        $processFactoryMock = $this->createProcessFactoryMock($process);

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())->method('remove')->with($toDir);

        $packagesSynchronizer = new PackagesSynchronizer($packagesDirProvider, $filesystem, $processFactoryMock);

        // Act
        $packagesSynchronizer->sync();
    }

    /**
     * @return void
     */
    public function testClearShouldInvokeRemove(): void
    {
        // Arrange
        $fromDir = DIRECTORY_SEPARATOR . PackagesDirProvider::FROM_DIR;
        $toDir = DIRECTORY_SEPARATOR . PackagesDirProvider::TO_DIR;
        $packagesDirProvider = $this->createPackagesDirProviderMock(['spryker'], $fromDir, $toDir);

        $process = $this->createMock(Process::class);
        $processFactoryMock = $this->createProcessFactoryMock($process);

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())->method('remove')->with($toDir);

        $packagesSynchronizer = new PackagesSynchronizer($packagesDirProvider, $filesystem, $processFactoryMock);

        // Act
        $packagesSynchronizer->clear();
    }

    /**
     * @param array<string> $dirs
     * @param string $fromDir
     * @param string $toDir
     *
     * @return \Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesDirProviderInterface
     */
    public function createPackagesDirProviderMock(array $dirs, string $fromDir, string $toDir): PackagesDirProviderInterface
    {
        $packagesDirProvider = $this->createMock(PackagesDirProviderInterface::class);
        $packagesDirProvider->method('getSprykerPackageDirs')->willReturn($dirs);
        $packagesDirProvider->method('getFromDir')->willReturn($fromDir);
        $packagesDirProvider->method('getToDir')->willReturn($toDir);

        return $packagesDirProvider;
    }

    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \Upgrade\Infrastructure\IO\ProcessFactory
     */
    public function createProcessFactoryMock(Process $process): ProcessFactory
    {
        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory->method('createFromShellCommandline')->willReturn($process);

        return $processFactory;
    }
}
