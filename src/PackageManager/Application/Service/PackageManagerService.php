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
use PackageManager\Domain\Client\ComposerLockDiffClientInterface;

class PackageManagerService implements PackageManagerServiceInterface
{
    /**
     * @var \PackageManager\Domain\Client\ComposerClientInterface
     */
    protected $packageManagerClient;

    /**
     * @var \PackageManager\Domain\Client\ComposerLockDiffClientInterface
     */
    protected $composerLockDiffClient;

    /**
     * @param \PackageManager\Domain\Client\ComposerClientInterface $packageManagerClient
     * @param \PackageManager\Domain\Client\ComposerLockDiffClientInterface $composerLockDiffClient
     */
    public function __construct(
        ComposerClientInterface $packageManagerClient,
        ComposerLockDiffClientInterface $composerLockDiffClient
    ) {
        $this->packageManagerClient = $packageManagerClient;
        $this->composerLockDiffClient = $composerLockDiffClient;
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

        if ($response->isSuccess()) {
            $composerLockDiffResponseCollection = $this->composerLockDiffClient->getComposerLockDiff();
            foreach ($composerLockDiffResponseCollection as $diffResponse) {
                $response = new PackageManagerResponseDto(
                    true,
                    $response->getOutput(),
                    $diffResponse->getPackageList(),
                );
                $responseCollection->add($response);
            }

            return $responseCollection;
        }

        $responseCollection->add($response);

        return $responseCollection;
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
