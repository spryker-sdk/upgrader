<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PackageManager\Domain\Client;

use PackageManager\Domain\Composer\ComposerCommandExecutorInterface;
use PackageManager\Domain\Composer\Reader\ComposerJsonReaderInterface;
use PackageManager\Domain\Composer\Reader\ComposerLockReaderInterface;
use PackageManager\Domain\Dto\Collection\PackageDtoCollection;
use PackageManager\Domain\Dto\PackageManagerResponseDto;

class ComposerClient implements ComposerClientInterface
{
    /**
     * @var string
     */
    protected const PACKAGES_KEY = 'packages';

    /**
     * @var string
     */
    protected const NAME_KEY = 'name';

    /**
     * @var string
     */
    protected const VERSION_KEY = 'version';

    /**
     * @var \PackageManager\Domain\Composer\ComposerCommandExecutorInterface
     */
    protected $composerCommandExecutor;

    /**
     * @var \PackageManager\Domain\Composer\Reader\ComposerJsonReaderInterface
     */
    protected $composerJsonReader;

    /**
     * @var \PackageManager\Domain\Composer\Reader\ComposerLockReaderInterface
     */
    protected $composerLockReader;

    /**
     * @param \PackageManager\Domain\Composer\ComposerCommandExecutorInterface $composerCallExecutor
     * @param \PackageManager\Domain\Composer\Reader\ComposerJsonReaderInterface $composerJsonReader
     * @param \PackageManager\Domain\Composer\Reader\ComposerLockReaderInterface $composerLockReader
     */
    public function __construct(
        ComposerCommandExecutorInterface $composerCallExecutor,
        ComposerJsonReaderInterface $composerJsonReader,
        ComposerLockReaderInterface $composerLockReader
    ) {
        $this->composerCommandExecutor = $composerCallExecutor;
        $this->composerJsonReader = $composerJsonReader;
        $this->composerLockReader = $composerLockReader;
    }

    /**
     * @return string
     */
    public function getProjectName(): string
    {
        $composerJsonContent = $this->composerJsonReader->read();

        return $composerJsonContent[self::NAME_KEY];
    }

    /**
     * @return array
     */
    public function getComposerJsonFile(): array
    {
        return $this->composerJsonReader->read();
    }

    /**
     * @return array
     */
    public function getComposerLockFile(): array
    {
        return $this->composerLockReader->read();
    }

    /**
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function require(PackageDtoCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->composerCommandExecutor->require($packageCollection);
    }

    /**
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function requireDev(PackageDtoCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->composerCommandExecutor->requireDev($packageCollection);
    }

    /**
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function update(): PackageManagerResponseDto
    {
        return $this->composerCommandExecutor->update();
    }

    /**
     * @param string $packageName
     *
     * @return string|null
     */
    public function getPackageVersion(string $packageName): ?string
    {
        $composerLock = $this->composerLockReader->read();

        foreach ($composerLock[self::PACKAGES_KEY] as $package) {
            if ($package[self::NAME_KEY] == $packageName) {
                return $package[self::VERSION_KEY];
            }
        }

        return null;
    }

    /**
     * @param string $packageName
     *
     * @return bool
     */
    public function isDevPackage(string $packageName): bool
    {
        $composerJson = $this->composerJsonReader->read();

        if (isset($composerJson['require-dev'][$packageName])) {
            return true;
        }

        return false;
    }
}
