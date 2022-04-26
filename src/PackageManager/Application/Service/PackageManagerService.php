<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PackageManager\Application\Service;

use PackageManager\Domain\Dto\Collection\PackageDtoCollection;
use PackageManager\Domain\Dto\Collection\PackageManagerResponseDtoCollection;
use PackageManager\Domain\Dto\PackageManagerResponseDto;
use PackageManager\Domain\Client\ComposerClientInterface;
use PackageManager\Domain\Client\ComposerLockComparatorClientInterface;
use PackageManager\Domain\Dto\ComposerLockDiffDto;

class PackageManagerService implements PackageManagerServiceInterface
{
    /**
     * @var \PackageManager\Domain\Client\ComposerClientInterface
     */
    protected $packageManagerClient;

    /**
     * @var \PackageManager\Domain\Client\ComposerLockComparatorClientInterface
     */
    protected $composerLockComparatorClint;

    /**
     * @param \PackageManager\Domain\Client\ComposerClientInterface $packageManagerClient
     * @param \PackageManager\Domain\Client\ComposerLockComparatorClientInterface $composerLockDiffClient
     */
    public function __construct(
        ComposerClientInterface $packageManagerClient,
        ComposerLockComparatorClientInterface $composerLockDiffClient
    ) {
        $this->packageManagerClient = $packageManagerClient;
        $this->composerLockComparatorClint = $composerLockDiffClient;
    }

    /**
     * @return string
     */
    public function getProjectName(): string
    {
        return $this->packageManagerClient->getProjectName();
    }

    /**
     * @return array
     */
    public function getComposerJsonFile(): array
    {
        return $this->packageManagerClient->getComposerJsonFile();
    }

    /**
     * @return array
     */
    public function getComposerLockFile(): array
    {
        return $this->packageManagerClient->getComposerLockFile();
    }

    /**
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function require(PackageDtoCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->packageManagerClient->require($packageCollection);
    }

    /**
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function requireDev(PackageDtoCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->packageManagerClient->requireDev($packageCollection);
    }

    /**
     * @return \PackageManager\Domain\Dto\Collection\PackageManagerResponseDtoCollection
     */
    public function update(): PackageManagerResponseDtoCollection
    {
        $responseCollection = new PackageManagerResponseDtoCollection();
        $response = $this->packageManagerClient->update();

        $responseCollection->add($response);

        return $responseCollection;
    }

    /**
     * @return ComposerLockDiffDto
     */
    public function getComposerLockDiff(): ComposerLockDiffDto
    {
        return $this->composerLockComparatorClint->getComposerLockDiff();
    }

    /**
     * @param string $packageName
     *
     * @return string|null
     */
    public function getPackageVersion(string $packageName): ?string
    {
        return $this->packageManagerClient->getPackageVersion($packageName);
    }

    /**
     * @param string $packageName
     *
     * @return bool
     */
    public function isDevPackage(string $packageName): bool
    {
        return $this->packageManagerClient->isDevPackage($packageName);
    }
}
