<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;

abstract class BaseReleaseGroupProcessor implements ReleaseGroupProcessorInterface
{
    protected EventDispatcherInterface $eventDispatcher;

    protected LoggerInterface $logger;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    protected function dispatchEvent(ReleaseGroupProcessorEvent $event, string $eventName): bool
    {
        $this->eventDispatcher->dispatch($event, $eventName);

        return $event->getStepsExecutionDto()->isSuccessful() || $event->getStepsExecutionDto()->getIsStopPropagation();
    }

    protected function addReleaseGroupStat(
        StepsResponseDto $stepsExecutionDto,
        PackageManagerResponseDto $packageManagerResponseDto
    ): StepsResponseDto {
        $stepsExecutionDto->getReleaseGroupStatDto()->setAppliedPackagesAmount(
            $stepsExecutionDto->getReleaseGroupStatDto()->getAppliedPackagesAmount() + $packageManagerResponseDto->getAppliedPackagesAmount(),
        );

        return $stepsExecutionDto;
    }

    protected function addAppliedRGsInfo(
        StepsResponseDto $stepsExecutionDto,
        int $appliedRGsNum,
        int $appliedSecurityRGsNum
    ): StepsResponseDto {
        $stepsExecutionDto->getReleaseGroupStatDto()->setAppliedRGsAmount($appliedRGsNum);
        $stepsExecutionDto->getReleaseGroupStatDto()->setAppliedSecurityFixesAmount($appliedSecurityRGsNum);

        $message = sprintf('Amount of applied release groups: %s', $appliedRGsNum);
        if ($appliedSecurityRGsNum) {
            $message .= sprintf(' (including %s security fix(es))', $appliedSecurityRGsNum);
        }

        $stepsExecutionDto->addOutputMessage($message);

        return $stepsExecutionDto;
    }
}
