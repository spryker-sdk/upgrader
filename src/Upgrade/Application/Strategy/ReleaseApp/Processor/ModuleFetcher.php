<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use RuntimeException;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface;
use Upgrade\Domain\Entity\Collection\PackageCollection;

class ModuleFetcher
{
    /**
     * @var string
     */
    public const MESSAGE_NO_PACKAGES_FOUND = 'No valid packages found';

    /**
     * @var \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManager;

    /**
     * @var \Upgrade\Application\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface
     */
    protected PackageCollectionMapperInterface $packageCollectionMapper;

    /**
     * @var iterable<\Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcherStrategy\ModuleFetcherStrategyInterface>
     */
    protected iterable $moduleFetcherStrategies;

    /**
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     * @param \Upgrade\Application\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface $packageCollectionMapper
     * @param iterable<\Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcherStrategy\ModuleFetcherStrategyInterface> $moduleFetcherStrategies
     */
    public function __construct(
        PackageManagerAdapterInterface $packageManager,
        PackageCollectionMapperInterface $packageCollectionMapper,
        iterable $moduleFetcherStrategies
    ) {
        $this->packageManager = $packageManager;
        $this->packageCollectionMapper = $packageCollectionMapper;
        $this->moduleFetcherStrategies = $moduleFetcherStrategies;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection $moduleCollection
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function require(ModuleDtoCollection $moduleCollection): PackageManagerResponseDto
    {
        $packageCollection = $this->packageCollectionMapper->mapModuleCollectionToPackageCollection($moduleCollection);

        if ($packageCollection->isEmpty()) {
            return new PackageManagerResponseDto(true, static::MESSAGE_NO_PACKAGES_FOUND);
        }

        return $this->requirePackageCollection($packageCollection);
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @throws \RuntimeException
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    protected function requirePackageCollection(PackageCollection $packageCollection): PackageManagerResponseDto
    {
        foreach ($this->moduleFetcherStrategies as $moduleFetcherStrategy) {
            if ($moduleFetcherStrategy->isApplicable()) {
                return $moduleFetcherStrategy->fetchModules($packageCollection);
            }
        }

        throw new RuntimeException('No applicable strategy has been found');
    }
}
