<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Commands;

use SprykerSdk\SdkContracts\Entity\ContextInterface;
use SprykerSdk\SdkContracts\Entity\ConverterInterface;
use SprykerSdk\SdkContracts\Entity\ExecutableCommandInterface;
use Upgrade\Application\Service\UpgradeService;
use Upgrade\Application\Service\UpgradeServiceInterface;
use Upgrade\Domain\Entity\Message;
use Upgrade\Infrastructure\Service\UpgradeServiceInterface;

class UpgradeCommand implements ExecutableCommandInterface
{
    /**
     * @var UpgradeServiceInterface
     */
    protected $upgradeService;

    /**
     * @param UpgradeServiceInterface $upgraderService
     */
    public function __construct(UpgradeServiceInterface $upgraderService)
    {
        $this->upgradeService = $upgraderService;
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'php';
    }

    /**
     * @return array<string>
     */
    public function getTags(): array
    {
        return [];
    }

    /**
     * @return bool
     */
    public function hasStopOnError(): bool
    {
        return true;
    }

    /**
     * @return \SprykerSdk\SdkContracts\Entity\ConverterInterface|null
     */
    public function getViolationConverter(): ?ConverterInterface
    {
        return null;
    }

    /**
     * @param \SprykerSdk\SdkContracts\Entity\ContextInterface $context
     *
     * @return \SprykerSdk\SdkContracts\Entity\ContextInterface
     */
    public function execute(ContextInterface $context): ContextInterface
    {
        $stepsExecutionDto = $this->upgradeService->upgrade();

        $message = new Message((string)$stepsExecutionDto->getOutputMessage());

        $context->addMessage(static::class, $message);

        return $context;
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return ContextInterface::DEFAULT_STAGE;
    }
}
