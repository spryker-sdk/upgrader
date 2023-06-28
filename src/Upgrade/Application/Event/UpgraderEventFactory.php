<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Event;

use Symfony\Component\Uid\Uuid;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Domain\ValueObject\Error;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class UpgraderEventFactory
{
    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var string|null
     */
    protected ?string $executionId = null;

    /**
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @return \Upgrade\Application\Event\UpgraderStartedEvent
     */
    public function createUpgraderStartedEvent(): UpgraderStartedEvent
    {
        return new UpgraderStartedEvent(
            time(),
            $this->configurationProvider->getOrganizationName(),
            $this->configurationProvider->getRepositoryName(),
            $this->getCiExecutionId(),
        );
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     * @param int $duration
     *
     * @return \Upgrade\Application\Event\UpgraderFinishedEvent
     */
    public function createUpgraderFinishedEvent(StepsResponseDto $stepsResponseDto, int $duration): UpgraderFinishedEvent
    {
        return new UpgraderFinishedEvent(
            time(),
            $duration,
            $this->configurationProvider->getOrganizationName(),
            $this->configurationProvider->getRepositoryName(),
            $stepsResponseDto->getError() !== null
                ? $stepsResponseDto->getError()->getErrorMessage()
                : '',
            $stepsResponseDto->getIsSuccessful(),
            $stepsResponseDto->getError() !== null && $stepsResponseDto->getError()->getErrorType() === Error::CLIENT_CODE_ERROR,
            $this->getCiExecutionId(),
        );
    }

    /**
     * @return string
     */
    protected function getCiExecutionId(): string
    {
        $ciExecutionId = $this->configurationProvider->getCiExecutionId();

        if ($ciExecutionId !== '') {
            return $ciExecutionId;
        }

        if ($this->executionId === null) {
            $this->executionId = (string)Uuid::v4();
        }

        return $this->executionId;
    }
}
