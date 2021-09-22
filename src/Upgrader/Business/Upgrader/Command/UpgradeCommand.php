<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Command;

use Exception;
use Upgrader\Business\Command\AbstractCommand;
use Upgrader\Business\Command\Response\Collection\CommandResponseCollection;
use Upgrader\Business\Command\Response\CommandResponse;
use Upgrader\Business\DataProvider\DataProviderInterface;
use Upgrader\Business\DataProvider\Entity\ReleaseGroup;
use Upgrader\Business\DataProvider\Request\DataProviderRequest;
use Upgrader\Business\DataProvider\Response\DataProviderResponse;
use Upgrader\Business\PackageManager\Entity\Collection\PackageCollection;
use Upgrader\Business\PackageManager\Entity\Package;
use Upgrader\Business\PackageManager\PackageManagerInterface;

class UpgradeCommand extends AbstractCommand
{
    /**
     * @return string
     */
    public function getCommand(): string
    {
        return 'upgrader upgrade';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'upgrader:upgrade';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'The command for upgrade Spryker packages';
    }

    /**
     * @var \Upgrader\Business\PackageManager\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @var \Upgrader\Business\DataProvider\DataProviderInterface
     */
    protected $dataProvider;

    /**
     * @param \Upgrader\Business\PackageManager\PackageManagerInterface $packageManager
     * @param \Upgrader\Business\DataProvider\DataProviderInterface $dataProvider
     */
    public function __construct(
        PackageManagerInterface $packageManager,
        DataProviderInterface $dataProvider
    ) {
        $this->dataProvider = $dataProvider;
        $this->packageManager = $packageManager;
    }

    /**
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function run(): CommandResponse
    {
        try {
            $dataProviderRequest = $this->createDataProviderRequest();
            $dataProviderResponse = $this->dataProvider->getNotInstalledReleaseGroupList($dataProviderRequest);
            $requireResponseCollection = $this->requirePackageCollection($dataProviderResponse);
        } catch (Exception $exception) {
            return $this->createResponse(false, $exception->getMessage());
        }

        return $this->createResponse($requireResponseCollection->isSuccess(), $requireResponseCollection->getOutput());
    }


    /**
     * @param bool $isSuccess
     * @param string $message
     *
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    protected function createResponse(bool $isSuccess, string $message): CommandResponse
    {
        return new CommandResponse($isSuccess, $this->getName(), $message);
    }
}
