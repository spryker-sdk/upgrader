<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\ClassExtendsUpdatedPackageChecker;

use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\ClassExtendsUpdatedPackageChecker;
use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\ProjectExtendedClassesFetcherInterface;
use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\VendorChangedClassesFetcherInterface;
use PHPUnit\Framework\TestCase;
use Upgrader\Configuration\ConfigurationProvider;

class ClassExtendsUpdatedPackageCheckerTest extends TestCase
{
    /**
     * @return void
     */
    public function testCheckShouldReturnNoViolationsWhenNotFoundInProject(): void
    {
        // Arrange
        $vendorChangedClassesFetcherMock = $this->createVendorChangedClassesFetcher(['Spryker\Zed\Acl\Business\Model' => 'spryker/acl']);
        $projectExtendedClassesFetcherMock = $this->createProjectExtendedClassesFetcher(['Spryker\Zed\Acl\AclConfig' => '/data/project/src/Pyz/Zed/Acl/AclConfig.php']);
        $configurationProvider = $this->createConfigurationProviderMock('/data/project/');

        $checker = new ClassExtendsUpdatedPackageChecker(
            $vendorChangedClassesFetcherMock,
            $projectExtendedClassesFetcherMock,
            $configurationProvider,
        );

        // Act
        $violations = $checker->check();

        // Assert
        $this->assertEmpty($violations);
    }

    /**
     * @return void
     */
    public function testCheckShouldReturnNViolationsWhenFoundExtendedClassInProject(): void
    {
        // Arrange
        $vendorChangedClassesFetcherMock = $this->createVendorChangedClassesFetcher(['Spryker\Zed\Acl\Business\Model' => 'spryker/acl']);
        $projectExtendedClassesFetcherMock = $this->createProjectExtendedClassesFetcher(['Spryker\Zed\Acl\Business\Model' => '/data/project/src/Pyz/Zed/Acl/Model.php']);
        $configurationProvider = $this->createConfigurationProviderMock('/data/project/');

        $checker = new ClassExtendsUpdatedPackageChecker(
            $vendorChangedClassesFetcherMock,
            $projectExtendedClassesFetcherMock,
            $configurationProvider,
        );

        // Act
        $violations = $checker->check();

        // Assert
        $this->assertCount(1, $violations);
        $this->assertSame('src/Pyz/Zed/Acl/Model.php', $violations[0]->getTarget());
        $this->assertSame('spryker/acl', $violations[0]->getPackage());
    }

    /**
     * @param array<string, string> $vendorChangedClasses
     *
     * @return \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\VendorChangedClassesFetcherInterface
     */
    public function createVendorChangedClassesFetcher(array $vendorChangedClasses): VendorChangedClassesFetcherInterface
    {
        $vendorChangedClassesFetcher = $this->createMock(VendorChangedClassesFetcherInterface::class);
        $vendorChangedClassesFetcher->method('fetchVendorChangedClassesWithPackage')->willReturn($vendorChangedClasses);

        return $vendorChangedClassesFetcher;
    }

    /**
     * @param array<string, string> $extendedClasses
     *
     * @return \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\ProjectExtendedClassesFetcherInterface
     */
    protected function createProjectExtendedClassesFetcher(array $extendedClasses): ProjectExtendedClassesFetcherInterface
    {
        $projectExtendedClassesFetcher = $this->createMock(ProjectExtendedClassesFetcherInterface::class);
        $projectExtendedClassesFetcher->method('fetchExtendedClasses')->willReturn($extendedClasses);

        return $projectExtendedClassesFetcher;
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
