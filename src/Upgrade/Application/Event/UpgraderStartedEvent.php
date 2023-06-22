<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Event;

use SprykerSdk\SdkContracts\Event\MetricEventInterface;

class UpgraderStartedEvent implements MetricEventInterface
{
    /**
     * @var string
     */
    public const EVENT_NAME = 'UpgraderStartedEvent';

    /**
     * @var int
     */
    protected int $timestamp;

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
    protected string $ciExecutionId;

    /**
     * @param int $timestamp
     * @param string $organizationName
     * @param string $repositoryName
     * @param string $ciExecutionId
     */
    public function __construct(int $timestamp, string $organizationName, string $repositoryName, string $ciExecutionId)
    {
        $this->timestamp = $timestamp;
        $this->organizationName = $organizationName;
        $this->repositoryName = $repositoryName;
        $this->ciExecutionId = $ciExecutionId;
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
            'organizationName' => $this->organizationName,
            'repositoryName' => $this->repositoryName,
            'ciExecutionId' => $this->ciExecutionId,
        ];
    }
}
