<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Client\Composer;

use Upgrader\Business\PackageManager\Client\Composer\Command\CommandInterface;
use Upgrader\Business\PackageManager\Client\Composer\Command\ComposerRequireCommandInterface;
use Upgrader\Business\PackageManager\Client\Composer\Json\Reader\ComposerJsonReaderInterface;
use Upgrader\Business\PackageManager\Client\Composer\Lock\Reader\ComposerLockReaderInterface;
use Upgrader\Business\PackageManager\Client\PackageManagerClientInterface;
use Upgrader\Business\PackageManager\Response\PackageManagerResponse;
use Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection;

class ComposerClient implements PackageManagerClientInterface
{
    protected const PACKAGES_KEY = 'packages';
    protected const NAME_KEY = 'name';
    protected const VERSION_KEY = 'version';

    /**
     * @var \Upgrader\Business\PackageManager\Client\Composer\Command\CommandInterface
     */
    protected $composerUpdateCommand;

    /**
     * @var \Upgrader\Business\PackageManager\Client\Composer\Command\ComposerRequireCommandInterface
     */
    protected $composerRequireCommand;

    /**
     * @var \Upgrader\Business\PackageManager\Client\Composer\Json\Reader\ComposerJsonReaderInterface
     */
    protected $composerJsonReader;

    /**
     * @var \Upgrader\Business\PackageManager\Client\Composer\Lock\Reader\ComposerLockReaderInterface
     */
    protected $composerLockReader;

    /**
     * @param \Upgrader\Business\PackageManager\Client\Composer\Command\CommandInterface $composerUpdateCommand
     * @param \Upgrader\Business\PackageManager\Client\Composer\Command\ComposerRequireCommandInterface $composerRequireCommand
     * @param \Upgrader\Business\PackageManager\Client\Composer\Json\Reader\ComposerJsonReaderInterface $composerJsonReader
     * @param \Upgrader\Business\PackageManager\Client\Composer\Lock\Reader\ComposerLockReaderInterface $composerLockReader
     */
    public function __construct(
        CommandInterface $composerUpdateCommand,
        ComposerRequireCommandInterface $composerRequireCommand,
        ComposerJsonReaderInterface $composerJsonReader,
        ComposerLockReaderInterface $composerLockReader
    ) {
        $this->composerUpdateCommand = $composerUpdateCommand;
        $this->composerRequireCommand = $composerRequireCommand;
        $this->composerJsonReader = $composerJsonReader;
        $this->composerLockReader = $composerLockReader;
    }

    /**
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    public function runUpdate(): PackageManagerResponse
    {
        return $this->composerUpdateCommand->run();
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
        $this->composerRequireCommand->setPackageCollection($packageCollection);

        return $this->composerRequireCommand->run();
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
}
