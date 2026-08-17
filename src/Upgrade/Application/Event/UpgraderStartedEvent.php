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
class UpgraderStartedEvent implements MetricEventInterface
{
    /**
     * @var string
     */
    public const EVENT_NAME = 'UpgraderStartedEvent';

    protected int $timestamp;

    protected string $organizationName;

    protected string $repositoryName;

    protected string $ciExecutionId;

    protected string $workspaceName;

    public function __construct(int $timestamp, string $organizationName, string $repositoryName, string $ciExecutionId, string $workspaceName)
    {
        $this->timestamp = $timestamp;
        $this->organizationName = $organizationName;
        $this->repositoryName = $repositoryName;
        $this->ciExecutionId = $ciExecutionId;
        $this->workspaceName = $workspaceName;
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
            'organizationName' => $this->organizationName,
            'repositoryName' => $this->repositoryName,
            'ciExecutionId' => $this->ciExecutionId,
            'workspaceName' => $this->workspaceName,
        ];
    }
}
