<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\PackageManagerPackagesDto;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\PackageManagerPackagesFetcher\PackageManagerPackagesFetcherInterface;
use Upgrade\Domain\Entity\Collection\PackageCollection;

class ModuleFetcher
{
    /**
     * @var string
     */
    public const MESSAGE_NO_PACKAGES_FOUND = 'No valid packages found';

    /**
     * @var string
     */
    protected const REQUIRED_TYPE = 'required';

    /**
     * @var string
     */
    protected const REQUIRED_DEV_TYPE = 'required-dev';

    protected PackageManagerAdapterInterface $packageManager;

    protected PackageCollectionMapperInterface $packageCollectionMapper;

    protected PackageManagerPackagesFetcherInterface $packageManagerPackagesFetcher;

    public function __construct(
        PackageManagerAdapterInterface $packageManager,
        PackageCollectionMapperInterface $packageCollectionMapper,
        PackageManagerPackagesFetcherInterface $packageManagerPackagesFetcher
    ) {
        $this->packageManager = $packageManager;
        $this->packageCollectionMapper = $packageCollectionMapper;
        $this->packageManagerPackagesFetcher = $packageManagerPackagesFetcher;
    }

    public function require(ModuleDtoCollection $moduleCollection): PackageManagerResponseDto
    {
        $packageCollection = $this->packageCollectionMapper->mapModuleCollectionToPackageCollection($moduleCollection);

        if ($packageCollection->isEmpty()) {
            return new PackageManagerResponseDto(true, static::MESSAGE_NO_PACKAGES_FOUND);
        }

        return $this->requirePackageCollection(
            $this->packageManagerPackagesFetcher->fetchPackages($packageCollection),
        );
    }

    protected function requirePackageCollection(PackageManagerPackagesDto $packageManagerPackagesDto): PackageManagerResponseDto
    {
        $requiredPackages = $packageManagerPackagesDto->getPackagesForRequire();
        $response = $this->requirePackages($requiredPackages, static::REQUIRED_TYPE);

        if (!$response->isSuccessful()) {
            return $response;
        }

        $responseSubPackages = $this->updateSubPackage($packageManagerPackagesDto->getPackagesForUpdate());

        if (!$responseSubPackages->isSuccessful()) {
            return $responseSubPackages;
        }

        $requiredDevPackages = $packageManagerPackagesDto->getPackagesForRequireDev();
        $responseDev = $this->requirePackages($requiredDevPackages, static::REQUIRED_DEV_TYPE);

        return new PackageManagerResponseDto(
            $responseDev->isSuccessful(),
            implode(PHP_EOL, [$response->getOutputMessage(), $responseSubPackages->getOutputMessage(), $responseDev->getOutputMessage()]),
            array_merge($response->getExecutedCommands(), $responseDev->getExecutedCommands(), $responseSubPackages->getExecutedCommands()),
            $requiredPackages->count() + $requiredDevPackages->count(),
        );
    }

    protected function updateSubPackage(PackageCollection $updatedSubPackages): PackageManagerResponseDto
    {
        if ($updatedSubPackages->isEmpty()) {
            return new PackageManagerResponseDto(true, 'There are no packages for the update.');
        }

        $requireResponse = $this->packageManager->updateSubPackage($updatedSubPackages);

        if (!$requireResponse->isSuccessful()) {
            return $requireResponse;
        }

        return new PackageManagerResponseDto(true, sprintf('Updated packages count: %s', $updatedSubPackages->count()), $requireResponse->getExecutedCommands());
    }

    protected function requirePackages(
        PackageCollection $requiredPackages,
        string $requiredPackageType
    ): PackageManagerResponseDto {
        if ($requiredPackages->isEmpty()) {
            return new PackageManagerResponseDto(true, sprintf('No new %s packages', $requiredPackageType));
        }

        $requireResponse = $requiredPackageType === static::REQUIRED_TYPE
            ? $this->packageManager->require($requiredPackages)
            : $this->packageManager->requireDev($requiredPackages);

        if (!$requireResponse->isSuccessful()) {
            return $requireResponse;
        }

        return new PackageManagerResponseDto(
            true,
            sprintf('Applied %s packages count: %s', $requiredPackageType, $requiredPackages->count()),
            $requireResponse->getExecutedCommands(),
        );
    }
}
