<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Step;

use Upgrade\Domain\Dto\Step\StepsExecutionDto;
use Upgrade\Domain\Strategy\ReleaseApp\Bridge\ReleaseAppClientBridge;
use Upgrade\Domain\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorInterface;
use Upgrade\Domain\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorResolver;
use Upgrade\Domain\Strategy\StepInterface;

class ReleaseGroupUpdateStep implements StepInterface
{
    /**
     * @var \Upgrade\Domain\Strategy\ReleaseApp\Bridge\ReleaseAppClientBridge
     */
    protected ReleaseAppClientBridge $packageManagementSystemBridge;

    /**
     * @var \Upgrade\Domain\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorInterface
     */
    protected ReleaseGroupRequireProcessorInterface $releaseGroupRequireProcessor;

    /**
     * @param \Upgrade\Domain\Strategy\ReleaseApp\Bridge\ReleaseAppClientBridge $packageManagementSystemBridge
     * @param \Upgrade\Domain\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorInterface $groupRequireProcessorResolver
     */
    public function __construct(
        ReleaseAppClientBridge        $packageManagementSystemBridge,
        ReleaseGroupRequireProcessorResolver $groupRequireProcessorResolver
    ) {
        $this->packageManagementSystemBridge = $packageManagementSystemBridge;
        $this->releaseGroupRequireProcessor = $groupRequireProcessorResolver->getProcessor();
    }

    /**
     * @param \Upgrade\Domain\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Domain\Dto\Step\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $dataProviderResponse = $this->packageManagementSystemBridge->getNotInstalledReleaseGroupList();
        $requireResponse = $this->releaseGroupRequireProcessor->requireCollection($dataProviderResponse->getReleaseGroupCollection());

        $stepsExecutionDto->addOutputMessage('ToDo: some major not installed');

        return $stepsExecutionDto;
    }
}
