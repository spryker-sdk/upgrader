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
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\HttpCommunicator;
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

    /**
     * @return \Upgrader\Business\Upgrader\UpgraderInterface
     */
    public function createUpgrader(): UpgraderInterface
    {
        return new Upgrader(
            $this->createPackageManager(),
            $this->createVersionControlSystem(),
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
     * @return \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\HttpCommunicator
     */
    public function createHttpCommunicator(): HttpCommunicator
    {
        return new HttpCommunicator(
            $this->getConfig(),
            $this->createCommunicationClient()
        );
    }

    /**
     * @return \GuzzleHttp\Client
     */
    protected function createCommunicationClient(): Client
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
     * @return \Upgrader\Business\Command\CommandInterface
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
