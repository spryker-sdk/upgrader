<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher;

use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\ClassMetaDataFromFileFetcherInterface;
use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\VendorChangedClassesFetcher;
use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\VendorChangedFilesFetcherInterface;
use PHPUnit\Framework\TestCase;

class VendorChangedClassesFetcherTest extends TestCase
{
    /**
     * @return void
     */
    public function testFetchVendorChangedClassesShouldSkipWhenClassNotFound(): void
    {
        // Arrange
        $packagesDiffFetcherMock = $this->createVendorChangedFilesFetcherMock(['/data/vendor/spryker/acl/src/Spryker/Zed/Acl/Business/Model/Group.php']);
        $classNameFromFileFetcher = $this->createClassMetaDataFromFileFetcherMock(null, 'spryker/acl');
        $vendorChangedClassesFetcher = new VendorChangedClassesFetcher(
            $packagesDiffFetcherMock,
            $classNameFromFileFetcher,
        );

        // Act
        $changedClasses = $vendorChangedClassesFetcher->fetchVendorChangedClasses();

        // Assert
        $this->assertEmpty($changedClasses);
    }

    /**
     * @return void
     */
    public function testFetchVendorChangedClassesShouldReturnClassesWhenClassFound(): void
    {
        // Arrange
        $packagesDiffFetcherMock = $this->createVendorChangedFilesFetcherMock(['/data/vendor/spryker/acl/src/Spryker/Zed/Acl/Business/Model/Group.php']);
        $classNameFromFileFetcher = $this->createClassMetaDataFromFileFetcherMock('Spryker\Zed\Acl\Business\Model\Group', 'spryker/acl');
        $vendorChangedClassesFetcher = new VendorChangedClassesFetcher(
            $packagesDiffFetcherMock,
            $classNameFromFileFetcher,
        );

        // Act
        $changedClasses = $vendorChangedClassesFetcher->fetchVendorChangedClasses();

        // Assert
        $this->assertSame(['Spryker\Zed\Acl\Business\Model\Group' => 'spryker/acl'], $changedClasses);
    }

    /**
     * @return void
     */
    public function testFetchVendorChangedClassesShouldReturnClassesWhenPackageNotFound(): void
    {
        // Arrange
        $packagesDiffFetcherMock = $this->createVendorChangedFilesFetcherMock(['/data/vendor/spryker/acl/src/Spryker/Zed/Acl/Business/Model/Group.php']);
        $classNameFromFileFetcher = $this->createClassMetaDataFromFileFetcherMock('Spryker\Zed\Acl\Business\Model\Group', null);
        $vendorChangedClassesFetcher = new VendorChangedClassesFetcher(
            $packagesDiffFetcherMock,
            $classNameFromFileFetcher,
        );

        // Act
        $changedClasses = $vendorChangedClassesFetcher->fetchVendorChangedClasses();

        // Assert
        $this->assertSame(['Spryker\Zed\Acl\Business\Model\Group' => '-'], $changedClasses);
    }

    /**
     * @param array<string> $changedFiles
     *
     * @return \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\VendorChangedFilesFetcherInterface
     */
    public function createVendorChangedFilesFetcherMock(array $changedFiles): VendorChangedFilesFetcherInterface
    {
        $vendorChangedFilesFetcher = $this->createMock(VendorChangedFilesFetcherInterface::class);
        $vendorChangedFilesFetcher->method('fetchChangedFiles')->willReturn($changedFiles);

        return $vendorChangedFilesFetcher;
    }

    /**
     * @param string|null $FQCN
     * @param string|null $packageName
     *
     * @return \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\ClassMetaDataFromFileFetcherInterface
     */
    public function createClassMetaDataFromFileFetcherMock(?string $FQCN, ?string $packageName): ClassMetaDataFromFileFetcherInterface
    {
        $classMetaDataFromFileFetcher = $this->createMock(ClassMetaDataFromFileFetcherInterface::class);
        $classMetaDataFromFileFetcher->method('fetchFQCN')->willReturn($FQCN);
        $classMetaDataFromFileFetcher->method('fetchPackageName')->willReturn($packageName);

        return $classMetaDataFromFileFetcher;
    }
}
