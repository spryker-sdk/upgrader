<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Step;

use Upgrade\Application\Dto\Step\StepsExecutionDto;
use Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Bridge\PackageManagementSystemBridge;
use Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Bridge\ReleaseGroupTransferBridgeInterface;
use Upgrade\Infrastructure\Processor\Strategy\StepInterface;

class ReleaseGroupUpdateStep implements StepInterface
{
    /**
     * @var \Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Bridge\PackageManagementSystemBridge
     */
    protected PackageManagementSystemBridge $packageManagementSystemBridge;

    /**
     * @var \Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Bridge\ReleaseGroupTransferBridgeInterface
     */
    protected ReleaseGroupTransferBridgeInterface $releaseGroupManager;

    /**
     * @param \Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Bridge\PackageManagementSystemBridge $packageManagementSystemBridge
     * @param \Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Bridge\ReleaseGroupTransferBridgeInterface $releaseGroupManager
     */
    public function __construct(
        PackageManagementSystemBridge $packageManagementSystemBridge,
        ReleaseGroupTransferBridgeInterface $releaseGroupManager
    ) {
        $this->packageManagementSystemBridge = $packageManagementSystemBridge;
        $this->releaseGroupManager = $releaseGroupManager;
    }

    /**
     * @param \Upgrade\Application\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\Step\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $dataProviderResponse = $this->packageManagementSystemBridge->getNotInstalledReleaseGroupList();
        $requireResponse = $this->releaseGroupManager->requireCollection($dataProviderResponse->getReleaseGroupCollection());

        return new StepsExecutionDto(true);
    }
}
