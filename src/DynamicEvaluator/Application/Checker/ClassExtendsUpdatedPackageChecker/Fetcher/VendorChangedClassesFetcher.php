<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher;

class VendorChangedClassesFetcher implements VendorChangedClassesFetcherInterface
{
    /**
     * @var \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\VendorChangedFilesFetcherInterface
     */
    protected VendorChangedFilesFetcherInterface $packagesDiffFetcher;

    /**
     * @var \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\ClassMetaDataFromFileFetcherInterface
     */
    protected ClassMetaDataFromFileFetcherInterface $classNameFromFileFetcher;

    /**
     * @param \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\VendorChangedFilesFetcherInterface $packagesDiffFetcher
     * @param \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Fetcher\ClassMetaDataFromFileFetcherInterface $classNameFromFileFetcher
     */
    public function __construct(
        VendorChangedFilesFetcherInterface $packagesDiffFetcher,
        ClassMetaDataFromFileFetcherInterface $classNameFromFileFetcher
    ) {
        $this->packagesDiffFetcher = $packagesDiffFetcher;
        $this->classNameFromFileFetcher = $classNameFromFileFetcher;
    }

    /**
     * @return array<string, string> Key - className. Value - packageName
     */
    public function fetchVendorChangedClasses(): array
    {
        $updatedClasses = [];

        foreach ($this->packagesDiffFetcher->fetchChangedFiles() as $changedFile) {
            $className = $this->classNameFromFileFetcher->fetchFQCN($changedFile);

            if ($className === null) {
                continue;
            }

            $updatedClasses[$className] = $this->classNameFromFileFetcher->fetchPackageName($changedFile) ?? '-';
        }

        return $updatedClasses;
    }
}
