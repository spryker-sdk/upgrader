<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business;

use Ergebnis\Json\Printer\Printer;
use Ergebnis\Json\Printer\PrinterInterface;
use GuzzleHttp\Client;
use Upgrader\Business\Command\CommandInterface;
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\HttpClient;
use Upgrader\Business\DataProvider\Client\ReleaseApp\ReleaseAppClient;
use Upgrader\Business\DataProvider\DataProvider;
use Upgrader\Business\PackageManager\Client\Composer\Command\ComposerRequireCommand;
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
use Upgrader\Business\Upgrader\Command\UpgradeCommand;
use Upgrader\Business\Upgrader\Upgrader;
use Upgrader\Business\Upgrader\UpgraderInterface;
use Upgrader\Business\VersionControlSystem\Client\Git\Command\GitAddCommand;
use Upgrader\Business\VersionControlSystem\Client\Git\Command\GitBranchCommand;
use Upgrader\Business\VersionControlSystem\Client\Git\Command\GitCheckoutToStartCommand;
use Upgrader\Business\VersionControlSystem\Client\Git\Command\GitCommitCommand;
use Upgrader\Business\VersionControlSystem\Client\Git\Command\GitPushCommand;
use Upgrader\Business\VersionControlSystem\Client\Git\Command\GitUpdateIndexCommand;
use Upgrader\UpgraderConfig;

class UpgraderBusinessFactory
{
    /**
     * @var \Upgrader\UpgraderConfig
     */
    protected $config;

    /**
     * @return \Upgrader\Business\Upgrader\UpgraderInterface
     */
    public function createUpgrader(): UpgraderInterface
    {
        return ( new Upgrader())
            ->addCommand($this->createGitUpdateIndexCommand())
            ->addCommand($this->createUpgradeCommand())
            ->addCommand($this->createGitBranchCommand())
            ->addCommand($this->createGitAddCommand())
            ->addCommand($this->createGitCommitCommand())
            ->addCommand($this->createGitPushCommand())
            ->addCommand($this->createGitCheckoutToStartCommand());
    }

    /**
     * @return \Upgrader\Business\Upgrader\Command\UpgradeCommand
     */
    public function createUpgradeCommand(): UpgradeCommand
    {
        return new UpgradeCommand(
            $this->createPackageManager(),
            $this->createDataProvider()
        );
    }

    /**
     * @return \Upgrader\Business\DataProvider\DataProvider
     */
    public function createDataProvider(): DataProvider
    {
        return new DataProvider(
            $this->createReleaseAppClient()
        );
    }

    /**
     * @return \Upgrader\Business\DataProvider\Client\ReleaseApp\ReleaseAppClient
     */
    public function createReleaseAppClient(): ReleaseAppClient
    {
        return new ReleaseAppClient(
            $this->createHttpCommunicator()
        );
    }

    /**
     * @return \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\HttpClient
     */
    public function createHttpCommunicator(): HttpClient
    {
        return new HttpClient(
            $this->getConfig(),
            $this->createCommunicationClient()
        );
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function createCommunicationClient(): Client
    {
        return new Client();
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
     * @return \Upgrader\Business\VersionControlSystem\Client\Git\Command\GitPushCommand
     */
    public function createGitPushCommand()
    {
        return new GitPushCommand($this->getConfig());
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\Client\Git\Command\GitCommitCommand
     */
    public function createGitCommitCommand(): GitCommitCommand
    {
        return new GitCommitCommand($this->getConfig());
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\Client\Git\Command\GitAddCommand
     */
    public function createGitAddCommand(): GitAddCommand
    {
        return new GitAddCommand($this->getConfig());
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\Client\Git\Command\GitBranchCommand
     */
    public function createGitBranchCommand(): GitBranchCommand
    {
        return new GitBranchCommand($this->getConfig());
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\Client\Git\Command\GitCheckoutToStartCommand
     */
    public function createGitCheckoutToStartCommand()
    {
        return new GitCheckoutToStartCommand($this->getConfig());
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
            $this->createComposerUpdateCommand(),
            $this->createComposerRequireCommand(),
            $this->createComposerJsonReader(),
            $this->createComposerLockReader()
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
     * @return \Upgrader\Business\PackageManager\Client\Composer\Command\ComposerRequireCommand
     */
    public function createComposerRequireCommand(): ComposerRequireCommand
    {
        return new ComposerRequireCommand($this->getConfig());
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
