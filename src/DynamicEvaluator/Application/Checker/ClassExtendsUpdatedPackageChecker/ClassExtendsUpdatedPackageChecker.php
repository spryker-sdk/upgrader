<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker;

use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Dto\ViolationDto;
use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\ProjectExtendedClassesFetcherInterface;
use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\VendorChangedClassesFetcherInterface;
use Upgrader\Configuration\ConfigurationProvider;

class ClassExtendsUpdatedPackageChecker
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE = 'These classes of yours extend modified Spryker Core classes and need to be checked';

    /**
     * @var \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\VendorChangedClassesFetcherInterface
     */
    protected VendorChangedClassesFetcherInterface $vendorChangedClassesFetcher;

    /**
     * @var \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\ProjectExtendedClassesFetcherInterface
     */
    protected ProjectExtendedClassesFetcherInterface $projectExtendedClassesFetcher;

    /**
     * @var \Upgrader\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @param \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\VendorChangedClassesFetcherInterface $vendorChangedClassesFetcher
     * @param \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\ProjectExtendedClassesFetcherInterface $projectExtendedClassesFetcher
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
     * @return array<\DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Dto\ViolationDto>
     */
    public function check(): array
    {
        $vendorChangedClasses = $this->vendorChangedClassesFetcher->fetchVendorChangedClassesWithPackage();

        $projectExtendedClasses = $this->projectExtendedClassesFetcher->fetchExtendedClasses();

        $rootPathLength = mb_strlen($this->configurationProvider->getRootPath());

        $violations = [];

        foreach ($projectExtendedClasses as $projectExtendedClass => $fileName) {
            if (!isset($vendorChangedClasses[$projectExtendedClass])) {
                continue;
            }

            $fileName = strpos($fileName, $this->configurationProvider->getRootPath()) === 0 ? substr($fileName, $rootPathLength) : $fileName;

            $violations[] = new ViolationDto(static::ERROR_MESSAGE, $fileName, $vendorChangedClasses[$projectExtendedClass]);
        }

        return $violations;
    }
}
