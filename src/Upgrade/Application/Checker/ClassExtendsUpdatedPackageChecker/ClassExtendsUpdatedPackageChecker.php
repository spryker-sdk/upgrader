<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker;

use Upgrade\Application\Checker\CheckerInterface;
use Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\ProjectExtendedClassesFetcherInterface;
use Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\VendorChangedClassesFetcherInterface;
use Upgrade\Application\Dto\ViolationDto;
use Upgrader\Configuration\ConfigurationProvider;

class ClassExtendsUpdatedPackageChecker implements CheckerInterface
{
    /**
     * @var \Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\VendorChangedClassesFetcherInterface
     */
    protected VendorChangedClassesFetcherInterface $vendorChangedClassesFetcher;

    /**
     * @var \Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\ProjectExtendedClassesFetcherInterface
     */
    protected ProjectExtendedClassesFetcherInterface $projectExtendedClassesFetcher;

    /**
     * @var \Upgrader\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @param \Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\VendorChangedClassesFetcherInterface $vendorChangedClassesFetcher
     * @param \Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\ProjectExtendedClassesFetcherInterface $projectExtendedClassesFetcher
     * @param \Upgrader\Configuration\ConfigurationProvider $configurationProvider
     */
    public function __construct(
        VendorChangedClassesFetcherInterface $vendorChangedClassesFetcher,
        ProjectExtendedClassesFetcherInterface $projectExtendedClassesFetcher,
        ConfigurationProvider $configurationProvider
    ) {
        $this->vendorChangedClassesFetcher = $vendorChangedClassesFetcher;
        $this->projectExtendedClassesFetcher = $projectExtendedClassesFetcher;
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @return array<\Upgrade\Application\Dto\ViolationDto>
     */
    public function check(): array
    {
        $vendorChangedClasses = $this->vendorChangedClassesFetcher->fetchVendorChangedClasses();

        $projectExtendedClasses = $this->projectExtendedClassesFetcher->fetchExtendedClasses();

        $rootPathLength = mb_strlen($this->configurationProvider->getRootPath());

        $violations = [];

        foreach ($projectExtendedClasses as $projectExtendedClass => $fileName) {
            if (!isset($vendorChangedClasses[$projectExtendedClass])) {
                continue;
            }

            $fileName = strpos($fileName, $this->configurationProvider->getRootPath()) === 0 ? substr($fileName, $rootPathLength) : $fileName;

            $violations[] = new ViolationDto('Project class extends class from updated package', $fileName, $vendorChangedClasses[$projectExtendedClass]);
        }

        return $violations;
    }
}