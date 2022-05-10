<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Step;

use Upgrade\Application\Provider\ReleaseAppClientProviderInterface;
use Upgrade\Domain\Dto\Step\StepsExecutionDto;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorResolver;
use Upgrade\Application\Strategy\StepInterface;

class ReleaseGroupUpdateStep implements StepInterface
{
    /**
     * @var \Upgrade\Application\Provider\ReleaseAppClientProviderInterface
     */
    protected ReleaseAppClientProviderInterface $packageManagementSystemBridge;

    /**
     * @var \Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorInterface
     */
    protected ReleaseGroupRequireProcessorInterface $releaseGroupRequireProcessor;

    /**
     * @param \Upgrade\Application\Provider\ReleaseAppClientProviderInterface $packageManagementSystemBridge
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupRequireProcessorResolver $groupRequireProcessorResolver
     */
    public function __construct(
        ReleaseAppClientProviderInterface    $packageManagementSystemBridge,
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

        return $this->releaseGroupRequireProcessor->requireCollection($dataProviderResponse->getReleaseGroupCollection(), $stepsExecutionDto);
    }
}
