<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManager;

use Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection;
use Upgrade\Application\Dto\PackageManager\Collection\PackageManagerResponseDtoCollection;
use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto;
use Upgrade\Infrastructure\PackageManager\Client\ComposerClientInterface;
use Upgrade\Infrastructure\PackageManager\Client\ComposerLockDiffClientInterface;

class PackageManager implements PackageManagerInterface
{
    /**
     * @var \Upgrade\Infrastructure\PackageManager\Client\ComposerClientInterface
     */
    protected $packageManagerClient;

    /**
     * @var \Upgrade\Infrastructure\PackageManager\Client\ComposerLockDiffClientInterface
     */
    protected $composerLockDiffClient;

    /**
     * @param \Upgrade\Infrastructure\PackageManager\Client\ComposerClientInterface $packageManagerClient
     * @param \Upgrade\Infrastructure\PackageManager\Client\ComposerLockDiffClientInterface $composerLockDiffClient
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
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto
     */
    public function require(PackageDtoCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->packageManagerClient->require($packageCollection);
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto
     */
    public function requireDev(PackageDtoCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->packageManagerClient->requireDev($packageCollection);
    }

    /**
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageManagerResponseDtoCollection
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
