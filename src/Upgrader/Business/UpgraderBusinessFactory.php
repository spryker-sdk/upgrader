<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business;

use Ergebnis\Json\Printer\Printer;
use Ergebnis\Json\Printer\PrinterInterface;
use Upgrader\Business\Command\AbstractCommand;
use Upgrader\Business\Command\CommandInterface;
use Upgrader\Business\PackageManager\Client\Composer\Command\ComposerUpdateCommand;
use Upgrader\Business\PackageManager\Client\Composer\ComposerClient;
use Upgrader\Business\PackageManager\Client\Composer\Json\Reader\ComposerJsonReader;
use Upgrader\Business\PackageManager\Client\Composer\Json\Reader\ComposerJsonReaderInterface;
use Upgrader\Business\PackageManager\Client\Composer\Json\Writer\ComposerJsonWriter;
use Upgrader\Business\PackageManager\Client\Composer\Json\Writer\ComposerJsonWriterInterface;
use Upgrader\Business\PackageManager\Client\Composer\Lock\Reader\ComposerLockReader;
use Upgrader\Business\PackageManager\Client\Composer\Lock\Reader\ComposerLockReaderInterface;
use Upgrader\Business\PackageManager\Client\PackageManagerClientInterface;
use Upgrader\Business\PackageManager\PackageManager;
use Upgrader\Business\PackageManager\PackageManagerInterface;
use Upgrader\Business\Upgrader\Upgrader;
use Upgrader\Business\Upgrader\UpgraderInterface;
use Upgrader\Business\VersionControlSystem\Client\Git\Command\GitUpdateIndexCommand;
use Upgrader\Business\VersionControlSystem\Client\Git\GitClient;
use Upgrader\Business\VersionControlSystem\Client\VersionControlSystemClientInterface;
use Upgrader\Business\VersionControlSystem\VersionControlSystem;
use Upgrader\Business\VersionControlSystem\VersionControlSystemInterface;
use Upgrader\UpgraderConfig;

class UpgraderBusinessFactory
{
    /**
     * @var \Upgrader\UpgraderConfig
     */
    protected $config;

//
//    /**
//     * @return \Upgrader\Business\GitClient\Command\UpdateIndexCommand
//     */
//    public function createUpdateIndexCommand(): UpdateIndexCommand
//    {
//        return new UpdateIndexCommand($this->getConfig());
//    }

    /**
     * @return \Upgrader\Business\Upgrader\UpgraderInterface
     */
    public function createUpgrader(): UpgraderInterface
    {
       return new Upgrader(
           $this->createPackageManager(),
           $this->createVersionControlSystem()
       );
    }

    /**
     * @return \Upgrader\Business\PackageManager\PackageManagerInterface
     */
    public function createPackageManager(): PackageManagerInterface
    {
        return new PackageManager(
            $this->createComposerClient()
        );
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\VersionControlSystemInterface
     */
    public function createVersionControlSystem(): VersionControlSystemInterface
    {
        return new VersionControlSystem(
            $this->createGitClient()
        );
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\Client\VersionControlSystemClientInterface
     */
    public function createGitClient(): VersionControlSystemClientInterface
    {
        return new GitClient(
            $this->createGitUpdateIndexCommand()
        );
    }

    /**
     * @return \Upgrader\Business\Command\CommandInterface
     */
    public function createGitUpdateIndexCommand(): CommandInterface
    {
        return new GitUpdateIndexCommand($this->getConfig());
    }

    /**
     * @return \Upgrader\Business\PackageManager\Client\PackageManagerClientInterface
     */
    public function createComposerClient(): PackageManagerClientInterface
    {
        return new ComposerClient(
            $this->createComposerUpdateCommand()
        );
    }

    /**
     * @return \Upgrader\Business\Command\CommandInterface
     */
    public function createComposerUpdateCommand(): CommandInterface
    {
        return new ComposerUpdateCommand($this->getConfig());
    }

    /**
     * @return \Upgrader\Business\PackageManager\Client\Composer\Json\Reader\ComposerJsonReaderInterface
     */
    public function createComposerJsonReader(): ComposerJsonReaderInterface
    {
        return new ComposerJsonReader();
    }

    /**
     * @return \Upgrader\Business\PackageManager\Client\Composer\Json\Writer\ComposerJsonWriterInterface
     */
    public function createComposerJsonWriter(): ComposerJsonWriterInterface
    {
        return new ComposerJsonWriter(
            $this->createPrinter()
        );
    }

    /**
     * @return \Upgrader\Business\PackageManager\Client\Composer\Lock\Reader\ComposerLockReaderInterface
     */
    public function createComposerLockReader(): ComposerLockReaderInterface
    {
        return new ComposerLockReader();
    }

    /**
     * @return \Ergebnis\Json\Printer\PrinterInterface
     */
    public function createPrinter(): PrinterInterface
    {
        return new Printer();
    }

    /**
     * @return \Upgrader\UpgraderConfig
     */
    public function getConfig(): UpgraderConfig
    {
        if ($this->config === null) {
            $this->config = new UpgraderConfig();
        }

        return $this->config;
    }
}
