<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business;

use Ergebnis\Json\Printer\Printer;
use Ergebnis\Json\Printer\PrinterInterface;
use GuzzleHttp\Client;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\Builder\HttpRequestBuilder;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\Builder\HttpResponseBuilder;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpClient;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpRequestExecutor;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\ReleaseAppClient;
use Upgrader\Business\PackageManagementSystem\PackageManagementSystem;
use Upgrader\Business\PackageManager\Client\Composer\ComposerCallExecutor;
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
use Upgrader\Business\Upgrader\Bridge\PackageManagementSystemBridge;
use Upgrader\Business\Upgrader\Bridge\ReleaseGroupTransferBridge;
use Upgrader\Business\Upgrader\Builder\PackageTransferCollectionBuilder;
use Upgrader\Business\Upgrader\Strategy\ComposerUpdateStrategy;
use Upgrader\Business\Upgrader\Strategy\ReleaseGroupStrategy;
use Upgrader\Business\Upgrader\Upgrader;
use Upgrader\Business\Upgrader\Validator\Package\AlreadyInstalledValidator;
use Upgrader\Business\Upgrader\Validator\PackageSoftValidator;
use Upgrader\Business\Upgrader\Validator\ReleaseGroup\MajorVersionValidator;
use Upgrader\Business\Upgrader\Validator\ReleaseGroup\ProjectChangesValidator;
use Upgrader\Business\Upgrader\Validator\ReleaseGroupSoftValidator;
use Upgrader\Business\VersionControlSystem\GitVcs;
use Upgrader\Business\VersionControlSystem\Provider\GitHub\GitHubProvider;
use Upgrader\Business\VersionControlSystem\Provider\ProviderInterface;
use Upgrader\Business\VersionControlSystem\VcsInterface;
use Upgrader\UpgraderConfig;

class UpgraderBusinessFactory
{
    /**
     * @var \Upgrader\UpgraderConfig
     */
    protected $config;

    /**
     * @return \Upgrader\Business\Upgrader\Upgrader
     */
    public function createUpgrader(): Upgrader
    {
        return new Upgrader(
            $this->createComposerUpdateStrategy(),
            $this->createReleaseGroupStrategy(),
            $this->createGitVcs()
        );
    }

    /**
     * @return \Upgrader\Business\Upgrader\Strategy\ComposerUpdateStrategy
     */
    public function createComposerUpdateStrategy(): ComposerUpdateStrategy
    {
        return new ComposerUpdateStrategy(
            $this->createPackageManager()
        );
    }

    /**
     * @return \Upgrader\Business\Upgrader\Strategy\ReleaseGroupStrategy
     */
    public function createReleaseGroupStrategy(): ReleaseGroupStrategy
    {
        return new ReleaseGroupStrategy(
            $this->createReleaseGroupManager(),
            $this->createDataProviderManager(),
        );
    }

    /**
     * @return \Upgrader\Business\Upgrader\Bridge\ReleaseGroupTransferBridge
     */
    public function createReleaseGroupManager(): ReleaseGroupTransferBridge
    {
        return new ReleaseGroupTransferBridge(
            $this->createReleaseGroupValidateManager(),
            $this->createPackageCollectionManager(),
            $this->createPackageManager()
        );
    }

    /**
     * @return \Upgrader\Business\Upgrader\Validator\ReleaseGroupSoftValidator
     */
    public function createReleaseGroupValidateManager(): ReleaseGroupSoftValidator
    {
        return new ReleaseGroupSoftValidator([
            new ProjectChangesValidator(),
            new MajorVersionValidator(),
        ]);
    }

    /**
     * @return \Upgrader\Business\Upgrader\Builder\PackageTransferCollectionBuilder
     */
    public function createPackageCollectionManager(): PackageTransferCollectionBuilder
    {
        return new PackageTransferCollectionBuilder(
            $this->createPackageValidateManager(),
            $this->createPackageManager()
        );
    }

    /**
     * @return \Upgrader\Business\Upgrader\Validator\PackageSoftValidator
     */
    public function createPackageValidateManager(): PackageSoftValidator
    {
        return new PackageSoftValidator([
            $this->createAlreadyInstalledValidator(),
        ]);
    }

    /**
     * @return \Upgrader\Business\Upgrader\Validator\Package\AlreadyInstalledValidator
     */
    public function createAlreadyInstalledValidator(): AlreadyInstalledValidator
    {
        return new AlreadyInstalledValidator(
            $this->createPackageManager()
        );
    }

    /**
     * @return \Upgrader\Business\Upgrader\Bridge\PackageManagementSystemBridge
     */
    public function createDataProviderManager(): PackageManagementSystemBridge
    {
        return new PackageManagementSystemBridge(
            $this->createDataProvider(),
            $this->createPackageManager()
        );
    }

    /**
     * @return \Upgrader\Business\PackageManagementSystem\PackageManagementSystem
     */
    public function createDataProvider(): PackageManagementSystem
    {
        return new PackageManagementSystem(
            $this->createReleaseAppClient()
        );
    }

    /**
     * @return \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\ReleaseAppClient
     */
    public function createReleaseAppClient(): ReleaseAppClient
    {
        return new ReleaseAppClient(
            $this->createHttpCommunicator()
        );
    }

    /**
     * @return \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpClient
     */
    public function createHttpCommunicator(): HttpClient
    {
        return new HttpClient(
            $this->createHttpRequestBuilder(),
            $this->createHttpResponseBuilder(),
            $this->createHttpRequestExecutor()
        );
    }

    /**
     * @return \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpRequestExecutor
     */
    public function createHttpRequestExecutor(): HttpRequestExecutor
    {
        return new HttpRequestExecutor(
            $this->createGuzzleClient(),
            $this->getConfig()
        );
    }

    /**
     * @return \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\Builder\HttpRequestBuilder
     */
    public function createHttpRequestBuilder(): HttpRequestBuilder
    {
        return new HttpRequestBuilder($this->getConfig());
    }

    /**
     * @return \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\Builder\HttpResponseBuilder
     */
    public function createHttpResponseBuilder(): HttpResponseBuilder
    {
        return new HttpResponseBuilder();
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function createGuzzleClient(): Client
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
     * @return \Upgrader\Business\PackageManager\Client\PackageManagerClientInterface
     */
    public function createComposerClient(): PackageManagerClientInterface
    {
        return new ComposerClient(
            $this->createComposerCallExecutor(),
            $this->createComposerJsonReader(),
            $this->createComposerLockReader()
        );
    }

    /**
     * @return \Upgrader\Business\PackageManager\Client\Composer\ComposerCallExecutor
     */
    public function createComposerCallExecutor(): ComposerCallExecutor
    {
        return new ComposerCallExecutor($this->getConfig());
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
     * @return \Upgrader\Business\VersionControlSystem\VcsInterface
     */
    public function createGitVcs(): VcsInterface
    {
        return new GitVcs($this->getConfig(), $this->createGitHubProvider());
    }

    /**
     * @return \Upgrader\Business\VersionControlSystem\Provider\ProviderInterface
     */
    public function createGitHubProvider(): ProviderInterface
    {
        return new GitHubProvider($this->getConfig());
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
