<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace PackageStorage\Application\Metric;

use PackageStorage\Application\Fetcher\ProjectExtendedClassesFetcherInterface;
use PackageStorage\Application\Fetcher\VendorChangedClassesFetcherInterface;
use Upgrade\Application\Dto\StepsResponseDto;

class ModuleStatisticUpdater implements ModuleStatisticUpdaterInterface
{
    /**
     * @var \PackageStorage\Application\Fetcher\ProjectExtendedClassesFetcherInterface
     */
    protected ProjectExtendedClassesFetcherInterface $projectExtendedClassesFetcher;

    /**
     * @var \PackageStorage\Application\Fetcher\VendorChangedClassesFetcherInterface
     */
    protected VendorChangedClassesFetcherInterface $vendorChangedClassesFetcher;

    /**
     * @param \PackageStorage\Application\Fetcher\ProjectExtendedClassesFetcherInterface $projectExtendedClassesFetcher
     * @param \PackageStorage\Application\Fetcher\VendorChangedClassesFetcherInterface $vendorChangedClassesFetcher
     */
    public function __construct(
        ProjectExtendedClassesFetcherInterface $projectExtendedClassesFetcher,
        VendorChangedClassesFetcherInterface $vendorChangedClassesFetcher
    ) {
        $this->projectExtendedClassesFetcher = $projectExtendedClassesFetcher;
        $this->vendorChangedClassesFetcher = $vendorChangedClassesFetcher;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function updateStatisticPreRequire(StepsResponseDto $stepsResponseDto): StepsResponseDto
    {
        $totalOverwritedModels = count($this->projectExtendedClassesFetcher->fetchExtendedClasses());
        $stepsResponseDto->getModelStatisticDto()->setTotalOverwrittenModels($totalOverwritedModels);

        return $stepsResponseDto;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function updateStatisticPostRequire(StepsResponseDto $stepsResponseDto): StepsResponseDto
    {
        $totalChangedCoreModules = $this->vendorChangedClassesFetcher->fetchVendorChangedClassesWithPackage();
        $projectExtendedClasses = $this->projectExtendedClassesFetcher->fetchExtendedClasses();
        $stepsResponseDto->getModelStatisticDto()->setTotalChangedModels(count($totalChangedCoreModules));

        $intersectedModuleNamespaces = array_intersect_key($projectExtendedClasses, $totalChangedCoreModules);
        $totalIntersectingModels = count($intersectedModuleNamespaces);
        $intersectedModuleNames = [];
        foreach ($intersectedModuleNamespaces as $intersectedModuleNamespace => $path) {
            $namespaceParts = explode('\\', $intersectedModuleNamespace);
            if (isset($namespaceParts[2])) {
                $intersectedModuleNames[] = $namespaceParts[2];
            }
        }

        $stepsResponseDto->getModelStatisticDto()->setIntersectingModels($intersectedModuleNames);
        $stepsResponseDto->getModelStatisticDto()->setTotalIntersectingModels($totalIntersectingModels);

        return $stepsResponseDto;
    }
}
