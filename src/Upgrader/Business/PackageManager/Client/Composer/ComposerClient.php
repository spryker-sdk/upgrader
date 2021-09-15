<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Client\Composer;

use Upgrader\Business\Command\CommandInterface;
use Upgrader\Business\Command\ResultOutput\CommandResultOutput;
use Upgrader\Business\PackageManager\Client\Composer\Json\Reader\ComposerJsonReaderInterface;
use Upgrader\Business\PackageManager\Client\Composer\Lock\Reader\ComposerLockReaderInterface;
use Upgrader\Business\PackageManager\Client\PackageManagerClientInterface;
use Upgrader\Business\PackageManager\Entity\Collection\PackageCollection;

class ComposerClient implements PackageManagerClientInterface
{
    /**
     * @var \Upgrader\Business\Command\CommandInterface
     */
    protected $composerUpdateCommand;

    /**
     * @var \Upgrader\Business\PackageManager\Client\Composer\Command\ComposerRequireCommand
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
     * @param \Upgrader\Business\Command\CommandInterface $composerUpdateCommand
     */
    public function __construct(
        CommandInterface $composerUpdateCommand,
        CommandInterface $composerRequireCommand,
        ComposerJsonReaderInterface $composerJsonReader,
        ComposerLockReaderInterface $composerLockReader
    ) {
        $this->composerUpdateCommand = $composerUpdateCommand;
        $this->composerRequireCommand = $composerRequireCommand;
        $this->composerJsonReader = $composerJsonReader;
        $this->composerLockReader = $composerLockReader;
    }

    /**
     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput
     */
    public function runUpdate(): CommandResultOutput
    {
        return $this->composerUpdateCommand->run();
    }

    /**
     * @return string
     */
    public function getProjectName(): string
    {
        $composerJsonContent = $this->composerJsonReader->read();

        return $composerJsonContent['name'];
    }

    public function getComposerJsonFile(): array
    {
        return $this->composerJsonReader->read();
    }

    /**
     * @return void
     */
    public function getComposerLockFile(): array
    {
        return $this->composerLockReader->read();
    }

    public function require(PackageCollection $packageCollection): CommandResultOutput
    {
        $this->composerRequireCommand->setPackageCollection($packageCollection);

        return $this->composerRequireCommand->run();
    }

    public function getPackageVersion(string $packageName): ?string
    {
        $composerLock = $this->composerLockReader->read();

        foreach ($composerLock['packages'] as $package) {
            if ($package['name'] == $packageName) {
                return $package['version'];
            }
        }

        return null;
    }
}
