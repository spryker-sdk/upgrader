<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Step;

use Upgrade\Application\Bridge\ReleaseAppClientBridgeInterface;
use Upgrade\Application\Dto\StepsExecutionDto;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorResolver;
use Upgrade\Application\Strategy\StepInterface;

class ReleaseGroupUpdateStep implements StepInterface
{
    /**
     * @var \Upgrade\Application\Bridge\ReleaseAppClientBridgeInterface
     */
    protected ReleaseAppClientBridgeInterface $packageManagementSystemBridge;

    /**
     * @var \Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorInterface
     */
    protected ReleaseGroupRequireProcessorInterface $releaseGroupRequireProcessor;

    /**
     * @param \Upgrade\Application\Bridge\ReleaseAppClientBridgeInterface $packageManagementSystemBridge
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorResolver $groupRequireProcessorResolver
     */
    public function __construct(
        ReleaseAppClientBridgeInterface $packageManagementSystemBridge,
        ReleaseGroupRequireProcessorResolver $groupRequireProcessorResolver
    ) {
        $this->packageManagementSystemBridge = $packageManagementSystemBridge;
        $this->releaseGroupRequireProcessor = $groupRequireProcessorResolver->getProcessor();
    }

    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $dataProviderResponse = $this->packageManagementSystemBridge->getNewReleaseGroups();

        return $this->releaseGroupRequireProcessor->requireCollection($dataProviderResponse->getReleaseGroupCollection(), $stepsExecutionDto);
    }
}
