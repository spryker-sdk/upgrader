<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Step;

use Upgrade\Application\Adapter\ReleaseAppClientAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupProcessorInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupProcessorResolver;
use Upgrade\Application\Strategy\StepInterface;

class ReleaseGroupUpdateStep implements StepInterface
{
    /**
     * @var \Upgrade\Application\Adapter\ReleaseAppClientAdapterInterface
     */
    protected ReleaseAppClientAdapterInterface $packageManagementSystemBridge;

    /**
     * @var \Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupProcessorInterface
     */
    protected ReleaseGroupProcessorInterface $releaseGroupProcessor;

    /**
     * @param \Upgrade\Application\Adapter\ReleaseAppClientAdapterInterface $packageManagementSystemBridge
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupProcessorResolver $groupRequireProcessorResolver
     */
    public function __construct(
        ReleaseAppClientAdapterInterface $packageManagementSystemBridge,
        ReleaseGroupProcessorResolver $groupRequireProcessorResolver
    ) {
        $this->packageManagementSystemBridge = $packageManagementSystemBridge;
        $this->releaseGroupProcessor = $groupRequireProcessorResolver->getProcessor();
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $requireRequestCollection = $this->packageManagementSystemBridge->getNewReleaseGroups()->getReleaseGroupCollection();

        $stepsExecutionDto->addOutputMessage(
            sprintf('Amount of available release groups for the project: %s', $requireRequestCollection->count()),
        );

        return $this->releaseGroupProcessor->process($requireRequestCollection, $stepsExecutionDto);
    }
}
