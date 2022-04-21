<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManager\Client\Composer;

use Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection;
use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto;
use Upgrade\Infrastructure\PackageManager\Client\Composer\Json\Reader\ComposerJsonReaderInterface;
use Upgrade\Infrastructure\PackageManager\Client\Composer\Lock\Reader\ComposerLockReaderInterface;
use Upgrade\Infrastructure\PackageManager\Client\ComposerClientInterface;

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
     * @var \Upgrade\Infrastructure\PackageManager\Client\Composer\ComposerCallExecutorInterface
     */
    protected $composerCallExecutor;

    /**
     * @var \Upgrade\Infrastructure\PackageManager\Client\Composer\Json\Reader\ComposerJsonReaderInterface
     */
    protected $composerJsonReader;

    /**
     * @var \Upgrade\Infrastructure\PackageManager\Client\Composer\Lock\Reader\ComposerLockReaderInterface
     */
    protected $composerLockReader;

    /**
     * @param \Upgrade\Infrastructure\PackageManager\Client\Composer\ComposerCallExecutorInterface $composerCallExecutor
     * @param \Upgrade\Infrastructure\PackageManager\Client\Composer\Json\Reader\ComposerJsonReaderInterface $composerJsonReader
     * @param \Upgrade\Infrastructure\PackageManager\Client\Composer\Lock\Reader\ComposerLockReaderInterface $composerLockReader
     */
    public function __construct(
        ComposerCallExecutorInterface $composerCallExecutor,
        ComposerJsonReaderInterface $composerJsonReader,
        ComposerLockReaderInterface $composerLockReader
    ) {
        $this->composerCallExecutor = $composerCallExecutor;
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
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto
     */
    public function require(PackageDtoCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->composerCallExecutor->require($packageCollection);
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto
     */
    public function requireDev(PackageDtoCollection $packageCollection): PackageManagerResponseDto
    {
        return $this->composerCallExecutor->requireDev($packageCollection);
    }

    /**
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto
     */
    public function update(): PackageManagerResponseDto
    {
        return $this->composerCallExecutor->update();
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
