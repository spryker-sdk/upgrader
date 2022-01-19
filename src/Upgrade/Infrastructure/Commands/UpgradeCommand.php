<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Commands;

use SprykerSdk\SdkContracts\Entity\ContextInterface;
use SprykerSdk\SdkContracts\Entity\ConverterInterface;
use SprykerSdk\SdkContracts\Entity\ExecutableCommandInterface;
use Upgrade\Application\Services\UpgraderServiceInterface;
use Upgrade\Domain\Entity\Message;

class UpgradeCommand implements ExecutableCommandInterface
{
    /**
     * @var \Upgrade\Application\Services\UpgraderServiceInterface
     */
    protected $upgraderService;

    /**
     * @param \Upgrade\Application\Services\UpgraderServiceInterface $upgraderService
     */
    public function __construct(UpgraderServiceInterface $upgraderService)
    {
        $this->upgraderService = $upgraderService;
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
        $stepsExecutionDto = $this->upgraderService->upgrade();

        $message = new Message((string)$stepsExecutionDto->getOutputMessage());

        $context->addMessage(static::class, $message);

        return $context;
    }
}
