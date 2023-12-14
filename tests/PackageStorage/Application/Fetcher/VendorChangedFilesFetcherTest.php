<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace PackageStorage\Application\Fetcher;

use PackageStorage\Application\PackagesSynchronizer\PackagesDirProvider;
use PackageStorage\Application\PackagesSynchronizer\PackagesDirProviderInterface;
use PackageStorage\Application\PublicApiFilePathsProvider\PublicApiFilePathsProviderInterface;
use PHPUnit\Framework\TestCase;
use SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface;
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

        $publicApiFilePathsProviderMock = $this->createPublicApiFilePathsProviderMock();

        $vendorChangedFilesFetcher = new VendorChangedFilesFetcher($packagesDirProviderMock, $processRunnerMock, $publicApiFilePathsProviderMock);

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

        $publicApiFilePathsProviderMock = $this->createPublicApiFilePathsProviderMock();

        $vendorChangedFilesFetcher = new VendorChangedFilesFetcher($packagesDirProviderMock, $processRunnerMock, $publicApiFilePathsProviderMock);

        // Act
        $files = $vendorChangedFilesFetcher->fetchChangedFiles();

        // Assert
        $this->assertEmpty($files);
    }

    /**
     * @param array<string> $dirs
     *
     * @return \PackageStorage\Application\PackagesSynchronizer\PackagesDirProviderInterface
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
     * @return \SprykerSdk\Utils\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    public function createProcessRunnerServiceMock(string $output): ProcessRunnerServiceInterface
    {
        $process = $this->createMock(Process::class);
        $process->method('getOutput')->willReturn($output);

        $processRunnerService = $this->createMock(ProcessRunnerServiceInterface::class);
        $processRunnerService->method('mustRunFromCommandLine')->willReturn($process);

        return $processRunnerService;
    }

    /**
     * @return \PackageStorage\Application\PublicApiFilePathsProvider\PublicApiFilePathsProviderInterface
     */
    public function createPublicApiFilePathsProviderMock(): PublicApiFilePathsProviderInterface
    {
        $publicApiFilePathsProvider = $this->createMock(PublicApiFilePathsProviderInterface::class);
        $publicApiFilePathsProvider->method('getPublicApiFilePathsRegexCollection')->willReturn(['someClass.php']);

        return $publicApiFilePathsProvider;
    }
}
