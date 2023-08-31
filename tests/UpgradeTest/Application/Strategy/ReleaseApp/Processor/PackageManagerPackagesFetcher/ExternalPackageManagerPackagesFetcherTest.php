<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\ReleaseApp\Processor\PackageManagerPackagesFetcher;

use PHPUnit\Framework\TestCase;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\PackageManagerPackagesFetcher\ExternalPackageManagerPackagesFetcher;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Domain\Entity\Package;

class ExternalPackageManagerPackagesFetcherTest extends TestCase
{
    /**
     * @return void
     */
    public function testFetchPackagesShouldReturnProperPackages(): void
    {
        // Arrange
        $packageCollection = new PackageCollection([
            new Package('spryker/dev', '1.2.0'),
            new Package('spryker/dev-major', '2.2.0'),
            new Package('spryker/package', '1.2.0'),
            new Package('spryker/package-major', '2.2.0'),
        ]);

        $packageManager = $this->createPackageManagerAdapterMock(['spryker/dev', 'spryker/dev-major']);

        $packagesFetcher = new ExternalPackageManagerPackagesFetcher($packageManager, false);

        // Act
        $packageManagerPackagesDto = $packagesFetcher->fetchPackages($packageCollection);

        // Assert
        $this->assertSame(0, $packageManagerPackagesDto->getPackagesForUpdate()->count());
        $this->assertSame(2, $packageManagerPackagesDto->getPackagesForRequire()->count());
        $this->assertSame(2, $packageManagerPackagesDto->getPackagesForRequireDev()->count());

        $this->assertSame($packageManagerPackagesDto->getPackagesForRequire()->toArray()[0]->getName(), 'spryker/package');
        $this->assertSame($packageManagerPackagesDto->getPackagesForRequire()->toArray()[1]->getName(), 'spryker/package-major');

        $this->assertSame($packageManagerPackagesDto->getPackagesForRequireDev()->toArray()[0]->getName(), 'spryker/dev');
        $this->assertSame($packageManagerPackagesDto->getPackagesForRequireDev()->toArray()[1]->getName(), 'spryker/dev-major');
    }

    /**
     * @return void
     */
    public function testIsApplicableShouldReturnFalseWhenIsReleaseGroupIntegratorEnabled(): void
    {
        // Arrange
        $packagesFetcher = new ExternalPackageManagerPackagesFetcher($this->createPackageManagerAdapterMock(), true);

        // Act
        $result = $packagesFetcher->isApplicable();

        // Assert
        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function testIsApplicableShouldReturnTrueWhenIsReleaseGroupIntegratorDisabled(): void
    {
        // Arrange
        $packagesFetcher = new ExternalPackageManagerPackagesFetcher($this->createPackageManagerAdapterMock(), false);

        // Act
        $result = $packagesFetcher->isApplicable();

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @param array<string> $devPackages
     *
     * @return \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected function createPackageManagerAdapterMock(array $devPackages = []): PackageManagerAdapterInterface
    {
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);

        $packageManagerAdapter
            ->method('isDevPackage')
            ->willReturnCallback(static fn (string $package): bool => in_array($package, $devPackages, true));

        return $packageManagerAdapter;
    }
}
