<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Client\Composer;

use Upgrader\Business\PackageManager\Client\Composer\Json\Reader\ComposerJsonReaderInterface;
use Upgrader\Business\PackageManager\Client\Composer\Lock\Reader\ComposerLockReaderInterface;
use Upgrader\Business\PackageManager\Client\PackageManagerClientInterface;
use Upgrader\Business\PackageManager\Response\PackageManagerResponse;
use Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection;

class ComposerClient implements PackageManagerClientInterface
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
     * @var \Upgrader\Business\PackageManager\Client\Composer\ComposerCallExecutorInterface
     */
    protected $composerCallExecutor;

    /**
     * @var \Upgrader\Business\PackageManager\Client\Composer\Json\Reader\ComposerJsonReaderInterface
     */
    protected $composerJsonReader;

    /**
     * @var \Upgrader\Business\PackageManager\Client\Composer\Lock\Reader\ComposerLockReaderInterface
     */
    protected $composerLockReader;

    /**
     * @param \Upgrader\Business\PackageManager\Client\Composer\ComposerCallExecutorInterface $composerCallExecutor
     * @param \Upgrader\Business\PackageManager\Client\Composer\Json\Reader\ComposerJsonReaderInterface $composerJsonReader
     * @param \Upgrader\Business\PackageManager\Client\Composer\Lock\Reader\ComposerLockReaderInterface $composerLockReader
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
     * @param \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection $packageCollection
     *
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    public function require(PackageTransferCollection $packageCollection): PackageManagerResponse
    {
        return $this->composerCallExecutor->require($packageCollection);
    }

    /**
     * @param \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection $packageCollection
     *
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    public function requireDev(PackageTransferCollection $packageCollection): PackageManagerResponse
    {
        return $this->composerCallExecutor->requireDev($packageCollection);
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
