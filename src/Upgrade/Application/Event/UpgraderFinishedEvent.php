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

    /**
     * @var int
     */
    protected int $timestamp;

    /**
     * @var int
     */
    protected int $duration;

    /**
     * @var string
     */
    protected string $organizationName;

    /**
     * @var string
     */
    protected string $repositoryName;

    /**
     * @var string
     */
    protected string $reason;

    /**
     * @var bool
     */
    protected bool $isBuildSuccessful;

    /**
     * @var bool
     */
    protected bool $isClientIssue;

    /**
     * @var string
     */
    protected string $ciExecutionId;

    /**
     * @var string
     */
    protected string $workspaceName;

    /**
     * @var int
     */
    protected int $availableRgsAmount;

    /**
     * @var int
     */
    protected int $appliedPackagesAmount;

    /**
     * @var int
     */
    protected int $appliedRGsAmount;

    /**
     * @param int $timestamp
     * @param int $duration
     * @param string $organizationName
     * @param string $repositoryName
     * @param string $reason
     * @param bool $isBuildSuccessful
     * @param bool $isClientIssue
     * @param string $ciExecutionId
     * @param string $workspaceName
     * @param int $availableRgsAmount
     * @param int $appliedPackagesAmount
     * @param int $appliedRGsAmount
     */
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

    /**
     * @return string
     */
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
