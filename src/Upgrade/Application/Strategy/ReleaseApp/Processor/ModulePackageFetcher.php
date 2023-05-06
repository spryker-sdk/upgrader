<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\ResponseDto;
use Upgrade\Application\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface;
use Upgrade\Domain\Entity\Collection\PackageCollection;

class ModulePackageFetcher
{
    /**
     * @var string
     */
    protected const REQUIRED_TYPE = 'required';

    /**
     * @var string
     */
    protected const REQUIRED_DEV_TYPE = 'required-dev';

    /**
     * @var \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManager;

    /**
     * @var \Upgrade\Application\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface
     */
    protected PackageCollectionMapperInterface $packageCollectionMapper;

    /**
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     * @param \Upgrade\Application\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface $packageCollectionMapper
     */
    public function __construct(
        PackageManagerAdapterInterface $packageManager,
        PackageCollectionMapperInterface $packageCollectionMapper
    ) {
        $this->packageManager = $packageManager;
        $this->packageCollectionMapper = $packageCollectionMapper;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection $moduleCollection
     *
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    public function require(ModuleDtoCollection $moduleCollection): ResponseDto
    {
        $packageCollection = $this->packageCollectionMapper->mapModuleCollectionToPackageCollection($moduleCollection);

        if ($packageCollection->isEmpty()) {
            return new ResponseDto(true, 'No valid packages found');
        }

        return $this->requirePackageCollection($packageCollection);
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    protected function requirePackageCollection(PackageCollection $packageCollection): ResponseDto
    {
        $requiredPackages = $this->packageCollectionMapper->getRequiredPackages($packageCollection);
        $requiredDevPackages = $this->packageCollectionMapper->getRequiredDevPackages($packageCollection);

        $response = $this->requirePackages($requiredPackages, static::REQUIRED_TYPE);

        if (!$response->isSuccessful()) {
            return $response;
        }

        $responseDev = $this->requirePackages($requiredDevPackages, static::REQUIRED_DEV_TYPE);

        return new ResponseDto(
            $responseDev->isSuccessful(),
            implode(PHP_EOL, [$response->getOutputMessage(), $responseDev->getOutputMessage()]),
        );
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $requiredPackages
     * @param string $requiredPackageType
     *
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    protected function requirePackages(
        PackageCollection $requiredPackages,
        string $requiredPackageType
    ): ResponseDto {
        if ($requiredPackages->isEmpty()) {
            return new ResponseDto(true, sprintf('No new %s packages', $requiredPackageType));
        }

        $requireResponse = $requiredPackageType === static::REQUIRED_TYPE
            ? $this->packageManager->require($requiredPackages)
            : $this->packageManager->requireDev($requiredPackages);

        if (!$requireResponse->isSuccessful()) {
            return $requireResponse;
        }

        return new ResponseDto(true, sprintf('Applied %s packages count: %s', $requiredPackageType, $requiredPackages->count()));
    }
}
