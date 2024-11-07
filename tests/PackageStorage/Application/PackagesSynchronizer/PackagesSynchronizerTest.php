<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace PackageStorage\Application\PackagesSynchronizer;

use PHPUnit\Framework\TestCase;
use SprykerSdk\Utils\Infrastructure\Service\Filesystem;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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
        $processRunnerMock = $this->createProcessRunnerServiceMock($process);

        $filesystem = $this->createMock(Filesystem::class);
        $invocations = [];
        $filesystem->expects($this->exactly(3))
            ->method('exists')
            ->willReturnCallback(function ($arg) use (&$invocations) {
                $invocations[] = $arg;

                return false;
            });
        $filesystem->expects($this->once())->method('mkdir')->with($toDir);

        $packagesSynchronizer = new PackagesSynchronizer($packagesDirProvider, $filesystem, $processRunnerMock);

        // Act
        $packagesSynchronizer->sync();

        // Assert
        $this->assertSame(
            [$toDir, $toDir . PackagesSynchronizer::GITIGNORE_FILE_NAME],
            $invocations,
        );
    }

    /**
     * @return void
     */
    public function testSyncShouldClearDataAndPassExceptionWhenExceptionThrown(): void
    {
        //Assert
        $this->expectException(ProcessFailedException::class);

        // Arrange
        $fromDir = DIRECTORY_SEPARATOR . PackagesDirProvider::FROM_DIR;
        $toDir = DIRECTORY_SEPARATOR . PackagesDirProvider::TO_DIR;
        $packagesDirProvider = $this->createPackagesDirProviderMock(['spryker'], $fromDir, $toDir);

        $process = $this->createMock(Process::class);

        $processRunnerMock = $this->createProcessRunnerServiceMock($process);
        $processRunnerMock->expects($this->once())
            ->method('mustRunFromCommandLine')
            ->willThrowException(new ProcessFailedException($process));

        $filesystem = $this->createMock(Filesystem::class);

        $returnValues = [true, true, true, false];
        $invocations = [];

        $filesystem->method('exists')
            ->willReturnCallback(function ($path) use (&$returnValues, &$invocations) {
                $invocations[] = $path;

                return array_shift($returnValues);
            });

        $filesystem->expects($this->once())
            ->method('remove')
            ->with($toDir);

        $packagesSynchronizer = new PackagesSynchronizer($packagesDirProvider, $filesystem, $processRunnerMock);

        // Act
        $packagesSynchronizer->sync();
    }

    /**
     * @return void
     */
    public function testPackagesSynchronizerDestruct(): void
    {
        // Arrange
        $packagesSynchronizer = $this->getMockBuilder(PackagesSynchronizer::class)
            ->disableOriginalConstructor()
            ->onlyMethods(array_diff(
                get_class_methods(PackagesSynchronizer::class),
                ['__destruct'],
            ))
            ->getMock();
        $packagesSynchronizer->expects($this->once())->method('clear');

        // Act
        $packagesSynchronizer->__destruct();
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
        $processRunnerMock = $this->createProcessRunnerServiceMock($process);

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->exactly(2))->method('exists')
            ->with($toDir)
            ->willReturnOnConsecutiveCalls(true, false);
        $filesystem->expects($this->once())->method('remove')->with($toDir);

        $packagesSynchronizer = new PackagesSynchronizer($packagesDirProvider, $filesystem, $processRunnerMock);

        // Act
        $packagesSynchronizer->clear();
    }

    /**
     * @return void
     */
    public function testClearShouldInvokeRemoveWhenFailed(): void
    {
        // Arrange
        $fromDir = DIRECTORY_SEPARATOR . PackagesDirProvider::FROM_DIR;
        $toDir = DIRECTORY_SEPARATOR . PackagesDirProvider::TO_DIR;
        $packagesDirProvider = $this->createPackagesDirProviderMock(['spryker'], $fromDir, $toDir);

        $process = $this->createMock(Process::class);
        $processRunnerMock = $this->createProcessRunnerServiceMock($process);

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->expects($this->once())->method('exists')
            ->with($toDir)
            ->willReturn(true);
        $filesystem->expects($this->once())->method('remove')->with($toDir);

        $packagesSynchronizer = new PackagesSynchronizer($packagesDirProvider, $filesystem, $processRunnerMock);
    }

    /**
     * @param array<string> $dirs
     * @param string $fromDir
     * @param string $toDir
     *
     * @return \PackageStorage\Application\PackagesSynchronizer\PackagesDirProviderInterface
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
     * @return \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    public function createProcessRunnerServiceMock(Process $process): ProcessRunnerServiceInterface
    {
        $processRunnerService = $this->createMock(ProcessRunnerServiceInterface::class);
        $processRunnerService->method('mustRunFromCommandLine')->willReturn($process);

        return $processRunnerService;
    }
}
