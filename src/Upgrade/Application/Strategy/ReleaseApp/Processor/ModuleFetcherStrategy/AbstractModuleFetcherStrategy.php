<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcherStrategy;

use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Domain\Entity\Package;

abstract class AbstractModuleFetcherStrategy implements ModuleFetcherStrategyInterface
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
     * @var bool
     */
    protected bool $isReleaseGroupIntegratorEnabled;

    /**
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     * @param bool $isReleaseGroupIntegratorEnabled
     */
    public function __construct(PackageManagerAdapterInterface $packageManager, bool $isReleaseGroupIntegratorEnabled = false)
    {
        $this->packageManager = $packageManager;
        $this->isReleaseGroupIntegratorEnabled = $isReleaseGroupIntegratorEnabled;
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function fetchModules(PackageCollection $packageCollection): PackageManagerResponseDto
    {
        $requiredPackages = $this->getRequiredPackages($packageCollection, [$this, 'isPackagedShouldBeRequired']);
        $response = $this->requirePackages($requiredPackages, static::REQUIRED_TYPE);

        if (!$response->isSuccessful()) {
            return $response;
        }

        $requiredDevPackages = $this->getRequiredPackages($packageCollection, [$this, 'isPackageShouldBeRequiredForDev']);
        $responseDev = $this->requirePackages($requiredDevPackages, static::REQUIRED_DEV_TYPE);

        if (!$responseDev->isSuccessful()) {
            return $responseDev;
        }

        $packagesForUpdate = $this->getPackagesForUpdate($packageCollection, array_merge($requiredPackages->toArray(), $requiredDevPackages->toArray()));
        $responseUpdatedPackages = $this->updateSubPackage($packagesForUpdate);

        return new PackageManagerResponseDto(
            $responseUpdatedPackages->isSuccessful(),
            implode(PHP_EOL, [$response->getOutputMessage(), $responseUpdatedPackages->getOutputMessage(), $responseDev->getOutputMessage()]),
            array_merge($response->getExecutedCommands(), $responseDev->getExecutedCommands(), $responseUpdatedPackages->getExecutedCommands()),
            $requiredPackages->count() + $requiredDevPackages->count(),
        );
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     * @param callable $isPackageShouldBeAdded
     *
     * @return \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    public function getRequiredPackages(PackageCollection $packageCollection, callable $isPackageShouldBeAdded): PackageCollection
    {
        $resultCollection = new PackageCollection();

        foreach ($packageCollection->toArray() as $package) {
            if ($isPackageShouldBeAdded($package)) {
                $resultCollection->add($package);
            }
        }

        return $resultCollection;
    }

    /**
     * @param \Upgrade\Domain\Entity\Package $package
     *
     * @return bool
     */
    abstract protected function isPackagedShouldBeRequired(Package $package): bool;

    /**
     * @param \Upgrade\Domain\Entity\Package $package
     *
     * @return bool
     */
    abstract protected function isPackageShouldBeRequiredForDev(Package $package): bool;

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     * @param array<\Upgrade\Domain\Entity\Package> $requiredPackages
     *
     * @return \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    protected function getPackagesForUpdate(PackageCollection $packageCollection, array $requiredPackages): PackageCollection
    {
        $resultCollection = new PackageCollection();

        foreach ($packageCollection->toArray() as $package) {
            if ($this->isPackageShouldBeUpdated($package, $requiredPackages)) {
                $resultCollection->add($package);
            }
        }

        return $resultCollection;
    }

    /**
     * @param \Upgrade\Domain\Entity\Package $package
     * @param array<\Upgrade\Domain\Entity\Package> $requiredPackages
     *
     * @return bool
     */
    abstract protected function isPackageShouldBeUpdated(Package $package, array $requiredPackages): bool;

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
