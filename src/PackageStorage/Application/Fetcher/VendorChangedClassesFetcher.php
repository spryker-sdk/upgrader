<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace PackageStorage\Application\Fetcher;

class VendorChangedClassesFetcher implements VendorChangedClassesFetcherInterface
{
    /**
     * @var \PackageStorage\Application\Fetcher\VendorChangedFilesFetcherInterface
     */
    protected VendorChangedFilesFetcherInterface $packagesDiffFetcher;

    /**
     * @var \PackageStorage\Application\Fetcher\ClassMetaDataFromFileFetcherInterface
     */
    protected ClassMetaDataFromFileFetcherInterface $classNameFromFileFetcher;

    /**
     * @param \PackageStorage\Application\Fetcher\VendorChangedFilesFetcherInterface $packagesDiffFetcher
     * @param \PackageStorage\Application\Fetcher\ClassMetaDataFromFileFetcherInterface $classNameFromFileFetcher
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
    public function fetchVendorChangedClassesWithPackage(): array
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
