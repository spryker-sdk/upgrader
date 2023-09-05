<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\ReleaseApp\Processor\PackageManagerPackagesFetcher;

use PHPUnit\Framework\TestCase;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\PackageManagerPackagesFetcher\PackageManagerPackagesFetcher;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Domain\Entity\Package;

class PackageManagerPackagesFetcherTest extends TestCase
{
    /**
     * Dev package should be updated
     *
     * @return void
     */
    public function testFetchPackagesShouldReturnUpdatedPackagesWhenDevPackageThatMatchConstraint(): void
    {
        // Arrange
        $packageCollection = new PackageCollection([
            new Package('spryker/dev', '1.2.0'),
        ]);

        $packageManager = $this->createPackageManagerAdapterMock(['spryker/dev'], ['spryker/dev' => '1.1.0'], ['spryker/dev' => '^1.1.0']);

        $packagesFetcher = new PackageManagerPackagesFetcher($packageManager, true);

        // Act
        $packageManagerPackagesDto = $packagesFetcher->fetchPackages($packageCollection);

        // Assert
        $this->assertSame(1, $packageManagerPackagesDto->getPackagesForUpdate()->count());
        $this->assertSame(0, $packageManagerPackagesDto->getPackagesForRequire()->count());
        $this->assertSame(0, $packageManagerPackagesDto->getPackagesForRequireDev()->count());

        $this->assertSame($packageManagerPackagesDto->getPackagesForUpdate()->toArray()[0]->getName(), 'spryker/dev');
    }

    /**
     * Dev package should be require
     *
     * @return void
     */
    public function testFetchPackagesShouldReturnRequiredPackagedWhenDevPackageThatDoesntMatchConstraint(): void
    {
        // Arrange
        $packageCollection = new PackageCollection([
            new Package('spryker/dev', '2.2.0'),
        ]);

        $packageManager = $this->createPackageManagerAdapterMock(['spryker/dev'], ['spryker/dev' => '1.1.0'], ['spryker/dev' => '^1.1.0']);

        $packagesFetcher = new PackageManagerPackagesFetcher($packageManager, true);

        // Act
        $packageManagerPackagesDto = $packagesFetcher->fetchPackages($packageCollection);

        // Assert
        $this->assertSame(0, $packageManagerPackagesDto->getPackagesForUpdate()->count());
        $this->assertSame(0, $packageManagerPackagesDto->getPackagesForRequire()->count());
        $this->assertSame(1, $packageManagerPackagesDto->getPackagesForRequireDev()->count());

        $this->assertSame($packageManagerPackagesDto->getPackagesForRequireDev()->toArray()[0]->getName(), 'spryker/dev');
    }

    /**
     * Package is not installed (package version === null)
     *
     * @return void
     */
    public function testFetchPackagesShouldReturnRequiredWhenPackageIsNotDevAndNotInstalled(): void
    {
        // Arrange
        $packageCollection = new PackageCollection([
            new Package('spryker/package', '1.2.0'),
        ]);

        $packageManager = $this->createPackageManagerAdapterMock([], ['spryker/package' => null]);

        $packagesFetcher = new PackageManagerPackagesFetcher($packageManager, true);

        // Act
        $packageManagerPackagesDto = $packagesFetcher->fetchPackages($packageCollection);

        // Assert
        $this->assertSame(0, $packageManagerPackagesDto->getPackagesForUpdate()->count());
        $this->assertSame(1, $packageManagerPackagesDto->getPackagesForRequire()->count());
        $this->assertSame(0, $packageManagerPackagesDto->getPackagesForRequireDev()->count());

        $this->assertSame($packageManagerPackagesDto->getPackagesForRequire()->toArray()[0]->getName(), 'spryker/package');
    }

    /**
     * Package is installed and match constraint
     *
     * @return void
     */
    public function testFetchPackagesShouldReturnRequiredWhenPackageIsNotDevAndInstalledAndMatchConstraint(): void
    {
        // Arrange
        $packageCollection = new PackageCollection([
            new Package('spryker/package', '1.2.0'),
        ]);

        $packageManager = $this->createPackageManagerAdapterMock([], ['spryker/package' => '1.1.1'], ['spryker/package' => '^1.1.1']);

        $packagesFetcher = new PackageManagerPackagesFetcher($packageManager, true);

        // Act
        $packageManagerPackagesDto = $packagesFetcher->fetchPackages($packageCollection);

        // Assert
        $this->assertSame(1, $packageManagerPackagesDto->getPackagesForUpdate()->count());
        $this->assertSame(0, $packageManagerPackagesDto->getPackagesForRequire()->count());
        $this->assertSame(0, $packageManagerPackagesDto->getPackagesForRequireDev()->count());

        $this->assertSame($packageManagerPackagesDto->getPackagesForUpdate()->toArray()[0]->getName(), 'spryker/package');
    }

    /**
     * Package is installed and doesn't match constraint
     *
     * @return void
     */
    public function testFetchPackagesShouldReturnRequiredWhenPackageIsNotDevAndInstalledAndDoesntMatchConstraint(): void
    {
        // Arrange
        $packageCollection = new PackageCollection([
            new Package('spryker/package', '2.2.0'),
        ]);

        $packageManager = $this->createPackageManagerAdapterMock([], ['spryker/package' => '1.1.1'], ['spryker/package' => '^1.1.1']);

        $packagesFetcher = new PackageManagerPackagesFetcher($packageManager, true);

        // Act
        $packageManagerPackagesDto = $packagesFetcher->fetchPackages($packageCollection);

        // Assert
        $this->assertSame(0, $packageManagerPackagesDto->getPackagesForUpdate()->count());
        $this->assertSame(1, $packageManagerPackagesDto->getPackagesForRequire()->count());
        $this->assertSame(0, $packageManagerPackagesDto->getPackagesForRequireDev()->count());

        $this->assertSame($packageManagerPackagesDto->getPackagesForRequire()->toArray()[0]->getName(), 'spryker/package');
    }

    /**
     * Package is installed and match constraint
     *
     * @return void
     */
    public function testFetchPackagesShouldReturnRequiredPackages(): void
    {
        // Arrange
        $packageCollection = new PackageCollection([
            new Package('spryker/dev', '1.2.0'),
            new Package('spryker/dev-major', '2.2.0'),
            new Package('spryker/package', '1.2.0'),
            new Package('spryker/package-new', '1.2.0'),
            new Package('spryker/package-major', '2.2.0'),
        ]);

        $packageManager = $this->createPackageManagerAdapterMock(
            ['spryker/dev', 'spryker/dev-major'],
            [
                'spryker/dev' => '1.1.0',
                'spryker/dev-major' => '2.1.1',
                'spryker/package' => '1.1.0',
                'spryker/package-new' => null,
                'spryker/package-major' => '1.1.0',
            ],
            [
                'spryker/dev' => '^1.1.0',
                'spryker/dev-major' => '^1.1.0',
                'spryker/package' => '^1.1.0',
                'spryker/package-new' => '^1.1.0',
                'spryker/package-major' => '^1.1.0',
            ],
        );

        $packagesFetcher = new PackageManagerPackagesFetcher($packageManager, true);

        // Act
        $packageManagerPackagesDto = $packagesFetcher->fetchPackages($packageCollection);

        // Assert
        $this->assertSame(2, $packageManagerPackagesDto->getPackagesForUpdate()->count());
        $this->assertSame(2, $packageManagerPackagesDto->getPackagesForRequire()->count());
        $this->assertSame(1, $packageManagerPackagesDto->getPackagesForRequireDev()->count());
    }

    /**
     * @param array<string> $devPackages
     * @param array<string, string|null> $packageVersions
     * @param array<string, string|null> $packageConstraints
     *
     * @return \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected function createPackageManagerAdapterMock(
        array $devPackages = [],
        array $packageVersions = [],
        array $packageConstraints = []
    ): PackageManagerAdapterInterface {
        $packageManagerAdapter = $this->createMock(PackageManagerAdapterInterface::class);

        $packageManagerAdapter
            ->method('isDevPackage')
            ->willReturnCallback(static fn (string $package): bool => in_array($package, $devPackages, true));

        $packageManagerAdapter
            ->method('getPackageVersion')
            ->willReturnCallback(static fn (string $package): ?string => $packageVersions[$package] ?? null);

        $packageManagerAdapter
            ->method('getPackageConstraint')
            ->willReturnCallback(static fn (string $package): ?string => $packageConstraints[$package] ?? null);

        return $packageManagerAdapter;
    }
}
