<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\PackageManager;

use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\ComposerLockDiffDto;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Infrastructure\PackageManager\CommandExecutor\ComposerCommandExecutorInterface;
use Upgrade\Infrastructure\PackageManager\CommandExecutor\ComposerLockComparatorCommandExecutorInterface;
use Upgrade\Infrastructure\PackageManager\Reader\ComposerReaderInterface;

class ComposerAdapter implements PackageManagerAdapterInterface
{
    /**
     * @var string
     */
    protected const PACKAGES_KEY = 'packages';

    /**
     * @var string
     */
    protected const PACKAGES_DEV_KEY = 'packages-dev';

    /**
     * @var string
     */
    protected const NAME_KEY = 'name';

    /**
     * @var string
     */
    protected const VERSION_KEY = 'version';

    protected ComposerCommandExecutorInterface $composerCommandExecutor;

    protected ComposerLockComparatorCommandExecutorInterface $composerLockComparator;

    protected ComposerReaderInterface $composerJsonReader;

    protected ComposerReaderInterface $composerLockReader;

    protected bool $isReleaseGroupIntegratorEnabled;

    public function __construct(
        ComposerCommandExecutorInterface $composerCommandExecutor,
        ComposerLockComparatorCommandExecutorInterface $composerLockComparator,
        ComposerReaderInterface $composerJsonReader,
        ComposerReaderInterface $composerLockReader,
        bool $isReleaseGroupIntegratorEnabled = false
    ) {
        $this->composerCommandExecutor = $composerCommandExecutor;
        $this->composerLockComparator = $composerLockComparator;
        $this->composerJsonReader = $composerJsonReader;
        $this->composerLockReader = $composerLockReader;
        $this->isReleaseGroupIntegratorEnabled = $isReleaseGroupIntegratorEnabled;
    }

    public function getProjectName(): string
    {
        $composerJsonContent = $this->composerJsonReader->read();

        return $composerJsonContent[self::NAME_KEY];
    }

    /**
     * @return array<mixed>
     */
    public function getComposerJsonFile(): array
    {
        return $this->composerJsonReader->read();
    }

    /**
     * @return array<mixed>
     */
    public function getComposerLockFile(): array
    {
        return $this->composerLockReader->read();
    }

    public function updateSubPackage(PackageCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->composerCommandExecutor->updateSubPackage($packageCollection);
    }

    public function require(PackageCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->composerCommandExecutor->require($packageCollection);
    }

    public function remove(PackageCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->composerCommandExecutor->remove($packageCollection);
    }

    public function requireDev(PackageCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->composerCommandExecutor->requireDev($packageCollection);
    }

    public function update(): PackageManagerResponseDto
    {
        return $this->composerCommandExecutor->update();
    }

    public function updateLockHash(): PackageManagerResponseDto
    {
        return $this->composerCommandExecutor->updateLockHash();
    }

    public function getPackageVersion(string $packageName): ?string
    {
        $composerLock = $this->composerLockReader->read();

        foreach ($composerLock[self::PACKAGES_KEY] as $package) {
            if ($package[self::NAME_KEY] == $packageName) {
                return $package[self::VERSION_KEY];
            }
        }

        foreach ($composerLock[self::PACKAGES_DEV_KEY] as $package) {
            if ($package[self::NAME_KEY] == $packageName) {
                return $package[self::VERSION_KEY];
            }
        }

        return null;
    }

    public function getPackageConstraint(string $packageName): ?string
    {
        $composerJson = $this->composerJsonReader->read();

        return $composerJson['require'][$packageName]
            ?? $composerJson['require-dev'][$packageName]
            ?? null;
    }

    public function isLockDevPackage(string $packageName): bool
    {
        $composerLock = $this->composerLockReader->read();

        return count(array_filter(
            $composerLock[self::PACKAGES_DEV_KEY],
            static fn (array $package): bool => $package[self::NAME_KEY] === $packageName,
        )) > 0;
    }

    public function isDevPackage(string $packageName): bool
    {
        $composerJson = $this->composerJsonReader->read();

        if (isset($composerJson['require-dev'][$packageName])) {
            return true;
        }

        return false;
    }

    public function isSubPackage(string $packageName): bool
    {
        if (!$this->isReleaseGroupIntegratorEnabled) {
            return false;
        }

        $composerJson = $this->composerJsonReader->read();

        if (!isset($composerJson['require'][$packageName]) && $this->getPackageVersion($packageName) !== null) {
            return true;
        }

        return false;
    }

    public function getComposerLockDiff(): ComposerLockDiffDto
    {
        return $this->composerLockComparator->getComposerLockDiff();
    }
}
