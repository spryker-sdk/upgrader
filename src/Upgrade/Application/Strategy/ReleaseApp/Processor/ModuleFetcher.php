<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
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
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    protected function requirePackageCollection(PackageCollection $packageCollection): PackageManagerResponseDto
    {
        $requiredPackages = $this->packageCollectionMapper->getRequiredPackages($packageCollection);
        $response = $this->requirePackages($requiredPackages, static::REQUIRED_TYPE);

        if (!$response->isSuccessful()) {
            return $response;
        }
        $subPackages = $this->packageCollectionMapper->getSubPackages($packageCollection);
        $responseSubPackages = $this->updateSubPackage($subPackages);

        if (!$responseSubPackages->isSuccessful()) {
            return $responseSubPackages;
        }

        $requiredDevPackages = $this->packageCollectionMapper->getRequiredDevPackages($packageCollection);
        $responseDev = $this->requirePackages($requiredDevPackages, static::REQUIRED_DEV_TYPE);

        return new PackageManagerResponseDto(
            $responseDev->isSuccessful(),
            implode(PHP_EOL, [$response->getOutputMessage(), $responseSubPackages->getOutputMessage(), $responseDev->getOutputMessage()]),
            array_merge($response->getExecutedCommands(), $responseDev->getExecutedCommands(), $responseSubPackages->getExecutedCommands()),
            $requiredPackages->count() + $requiredDevPackages->count(),
        );
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $updatedSubPackages
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
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

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $requiredPackages
     * @param string $requiredPackageType
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
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
