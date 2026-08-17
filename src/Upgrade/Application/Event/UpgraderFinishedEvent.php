<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Event;

use SprykerSdk\SdkContracts\Event\MetricEventInterface;

/**
 * @codeCoverageIgnore
 */
class UpgraderFinishedEvent implements MetricEventInterface
{
    /**
     * @var string
     */
    public const EVENT_NAME = 'UpgraderFinishedEvent';

    protected int $timestamp;

    protected int $duration;

    protected string $organizationName;

    protected string $repositoryName;

    protected string $reason;

    protected bool $isBuildSuccessful;

    protected bool $isClientIssue;

    protected string $ciExecutionId;

    protected string $workspaceName;

    protected int $availableRgsAmount;

    protected int $appliedPackagesAmount;

    protected int $appliedRGsAmount;

    public function __construct(
        int $timestamp,
        int $duration,
        string $organizationName,
        string $repositoryName,
        string $reason,
        bool $isBuildSuccessful,
        bool $isClientIssue,
        string $ciExecutionId,
        string $workspaceName,
        int $availableRgsAmount,
        int $appliedPackagesAmount,
        int $appliedRGsAmount
    ) {
        $this->timestamp = $timestamp;
        $this->duration = $duration;
        $this->organizationName = $organizationName;
        $this->repositoryName = $repositoryName;
        $this->reason = $reason;
        $this->isBuildSuccessful = $isBuildSuccessful;
        $this->isClientIssue = $isClientIssue;
        $this->ciExecutionId = $ciExecutionId;
        $this->workspaceName = $workspaceName;
        $this->availableRgsAmount = $availableRgsAmount;
        $this->appliedPackagesAmount = $appliedPackagesAmount;
        $this->appliedRGsAmount = $appliedRGsAmount;
    }

    public function getName(): string
    {
        return static::EVENT_NAME;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPayLoad(): array
    {
        return [
            'time' => $this->timestamp,
            'duration' => $this->duration,
            'organizationName' => $this->organizationName,
            'repositoryName' => $this->repositoryName,
            'reason' => $this->reason,
            'isBuildSuccessful' => $this->isBuildSuccessful,
            'isClientIssue' => $this->isClientIssue,
            'ciExecutionId' => $this->ciExecutionId,
            'workspaceName' => $this->workspaceName,
            'availableRgsAmount' => $this->availableRgsAmount,
            'appliedPackages' => $this->appliedPackagesAmount,
            'appliedRGs' => $this->appliedRGsAmount,
        ];
    }
}
