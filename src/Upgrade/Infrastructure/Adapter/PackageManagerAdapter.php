<?php

namespace Upgrade\Infrastructure\Adapter;

use PackageManager\Application\Service\PackageManagerServiceInterface;
use PackageManager\Domain\Dto\Collection\PackageDtoCollection;
use PackageManager\Domain\Dto\Collection\PackageManagerResponseDtoCollection;
use PackageManager\Domain\Dto\PackageManagerResponseDto;
use Upgrade\Domain\Adapter\PackageManagerAdapterInterface;

class PackageManagerAdapter implements PackageManagerAdapterInterface
{
    protected PackageManagerServiceInterface $packageManager;

    /**
     * @param PackageManagerServiceInterface $packageManager
     */
    public function __construct(PackageManagerServiceInterface $packageManager)
    {
        $this->packageManager = $packageManager;
    }

    public function getProjectName(): string
    {
        return $this->packageManager->getProjectName();
    }

    public function getComposerJsonFile(): array
    {
        return $this->packageManager->getComposerJsonFile();
    }

    public function getComposerLockFile(): array
    {
        return $this->packageManager->getComposerLockFile();
    }

    public function require(PackageDtoCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->packageManager->require($packageCollection);
    }

    public function requireDev(PackageDtoCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->packageManager->requireDev($packageCollection);
    }

    public function update(): PackageManagerResponseDtoCollection
    {
        return $this->packageManager->update();
    }

    public function getPackageVersion(string $packageName): ?string
    {
        return $this->packageManager->getPackageVersion($packageName);
    }

    public function isDevPackage(string $packageName): bool
    {
        return $this->packageManager->isDevPackage($packageName);
    }
}
