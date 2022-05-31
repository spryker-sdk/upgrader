<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Tasks\Upgrade;

use SprykerSdk\SdkContracts\Entity\Lifecycle\LifecycleInterface;
use SprykerSdk\SdkContracts\Entity\TaskInterface;
use Upgrade\Application\Service\UpgradeServiceInterface;
use Upgrader\Commands\Upgrade\UpgradeCommand;
use Upgrader\Lifecycle\Lifecycle;

class UpgradeTask implements TaskInterface
{
    /**
     * @var \Upgrade\Application\Service\UpgradeServiceInterface
     */
    protected $upgraderService;

    /**
     * @param \Upgrade\Application\Service\UpgradeServiceInterface $upgraderService
     */
    public function __construct(UpgradeServiceInterface $upgraderService)
    {
        $this->upgraderService = $upgraderService;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return 'upgradability:php:upgrade';
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return 'Upgrades your system to the latest Spryker version (minor + patches)';
    }

    /**
     * @return array<\SprykerSdk\SdkContracts\Entity\CommandInterface>
     */
    public function getCommands(): array
    {
        return [
            new UpgradeCommand($this->upgraderService),
        ];
    }

    /**
     * @return array<\SprykerSdk\SdkContracts\Entity\PlaceholderInterface>
     */
    public function getPlaceholders(): array
    {
        return [];
    }

    /**
     * @return string|null
     */
    public function getHelp(): ?string
    {
        return 'Helps you don\'t think about updates.';
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return '0.1.0';
    }

    /**
     * @return bool
     */
    public function isDeprecated(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isOptional(): bool
    {
        return false;
    }

    /**
     * @return string|null
     */
    public function getSuccessor(): ?string
    {
        return null;
    }

    /**
     * @return \SprykerSdk\SdkContracts\Entity\Lifecycle\LifecycleInterface
     */
    public function getLifecycle(): LifecycleInterface
    {
        return new Lifecycle();
    }

    /**
     * @return array<string>
     */
    public function getStages(): array
    {
        return [];
    }
}
