<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher;

use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\VendorChangedFilesFetcher;
use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesDirProvider;
use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesDirProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class VendorChangedFilesFetcherTest extends TestCase
{
    /**
     * @return void
     */
    public function testFetchChangedFilesShouldReturnVendorFiles(): void
    {
        // Arrange
        $packagesDirProviderMock = $this->createPackagesDirProviderMock(['spryker']);
        $processRunnerMock = $this->createProcessRunnerServiceMock(
            <<<OUT
            src/Spryker/Zed/Acl/Business/Model/Group.php
            src/Spryker/Zed/Acl/Business/Model/Installer.php
            OUT,
        );

        $vendorChangedFilesFetcher = new VendorChangedFilesFetcher($packagesDirProviderMock, $processRunnerMock);

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
        $processRunnerMock = $this->createProcessRunnerServiceMock('');

        $vendorChangedFilesFetcher = new VendorChangedFilesFetcher($packagesDirProviderMock, $processRunnerMock);

        // Act
        $files = $vendorChangedFilesFetcher->fetchChangedFiles();

        // Assert
        $this->assertEmpty($files);
    }

    /**
     * @param array<string> $dirs
     *
     * @return \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesDirProviderInterface
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
     * @return \Core\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    public function createProcessRunnerServiceMock(string $output): ProcessRunnerServiceInterface
    {
        $process = $this->createMock(Process::class);
        $process->method('getOutput')->willReturn($output);

        $processRunnerService = $this->createMock(ProcessRunnerServiceInterface::class);
        $processRunnerService->method('mustRunFromCommandLine')->willReturn($process);

        return $processRunnerService;
    }
}
