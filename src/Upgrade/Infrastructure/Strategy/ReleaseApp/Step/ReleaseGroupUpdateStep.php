<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Step;

use Upgrade\Application\Dto\Step\StepsExecutionDto;
use Upgrade\Infrastructure\Strategy\ReleaseApp\Bridge\ReleaseAppClientBridge;
use Upgrade\Infrastructure\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorInterface;
use Upgrade\Infrastructure\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorResolver;
use Upgrade\Infrastructure\Strategy\StepInterface;

class ReleaseGroupUpdateStep implements StepInterface
{
    /**
     * @var \Upgrade\Infrastructure\Strategy\ReleaseApp\Bridge\ReleaseAppClientBridge
     */
    protected ReleaseAppClientBridge $packageManagementSystemBridge;

    /**
     * @var \Upgrade\Infrastructure\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorInterface
     */
    protected ReleaseGroupRequireProcessorInterface $releaseGroupRequireProcessor;

    /**
     * @param \Upgrade\Infrastructure\Strategy\ReleaseApp\Bridge\ReleaseAppClientBridge $packageManagementSystemBridge
     * @param \Upgrade\Infrastructure\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorInterface $groupRequireProcessorResolver
     */
    public function __construct(
        ReleaseAppClientBridge        $packageManagementSystemBridge,
        ReleaseGroupRequireProcessorResolver $groupRequireProcessorResolver
    ) {
        $this->packageManagementSystemBridge = $packageManagementSystemBridge;
        $this->releaseGroupRequireProcessor = $groupRequireProcessorResolver->getProcessor();
    }

    /**
     * @param \Upgrade\Application\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\Step\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $dataProviderResponse = $this->packageManagementSystemBridge->getNotInstalledReleaseGroupList();
        $requireResponse = $this->releaseGroupRequireProcessor->requireCollection($dataProviderResponse->getReleaseGroupCollection());

        $stepsExecutionDto->addOutputMessage('ToDo: some major not installed');

        return $stepsExecutionDto;
    }
}
