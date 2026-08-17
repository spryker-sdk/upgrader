<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Step;

use Psr\Log\LoggerInterface;
use Upgrade\Application\Adapter\ReleaseAppClientAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\ReleaseApp\Processor\ReleaseGroupProcessorResolver;
use Upgrade\Application\Strategy\StepInterface;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class ReleaseGroupUpdateStep implements StepInterface
{
    protected ReleaseAppClientAdapterInterface $packageManagementSystemBridge;

    protected ReleaseGroupProcessorResolver $groupRequireProcessorResolver;

    private ConfigurationProvider $configurationProvider;

    protected LoggerInterface $logger;

    public function __construct(
        ReleaseAppClientAdapterInterface $packageManagementSystemBridge,
        ReleaseGroupProcessorResolver $groupRequireProcessorResolver,
        ConfigurationProvider $configurationProvider,
        LoggerInterface $logger
    ) {
        $this->packageManagementSystemBridge = $packageManagementSystemBridge;
        $this->groupRequireProcessorResolver = $groupRequireProcessorResolver;
        $this->configurationProvider = $configurationProvider;
        $this->logger = $logger;
    }

    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $releaseGroupId = $this->configurationProvider->getReleaseGroupId();

        $requireRequestCollection = $releaseGroupId !== null
            ? $this->packageManagementSystemBridge->getReleaseGroup($releaseGroupId)->getReleaseGroupCollection()
            : $this->packageManagementSystemBridge->getNewReleaseGroups()->getReleaseGroupCollection();

        $stepsExecutionDto->addOutputMessage(
            sprintf('Amount of available release groups for the project: %s', $requireRequestCollection->count()),
        );

        $stepsExecutionDto->getReleaseGroupStatDto()->setAvailableRgsAmount($requireRequestCollection->count());

        $releaseGroupProcessor = $this->groupRequireProcessorResolver->getProcessor();
        $this->logger->info(sprintf('Run release group processor `%s`', $releaseGroupProcessor->getProcessorName()));

        return $releaseGroupProcessor->process($requireRequestCollection, $stepsExecutionDto);
    }
}
