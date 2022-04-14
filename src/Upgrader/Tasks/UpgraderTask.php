<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Tasks;

use Codebase\Infrastructure\Service\CodebaseService;
use CodeCompliance\Application\Service\CodeComplianceServiceInterface;
use SprykerSdk\SdkContracts\Entity\Lifecycle\LifecycleInterface;
use SprykerSdk\SdkContracts\Entity\TaskInterface;
use Upgrade\Application\Services\UpgraderServiceInterface;
use Upgrader\Commands\Evaluate\Analyze\AnalyzeCommand;
use Upgrader\Commands\Evaluate\Report\ReportCommand;
use Upgrader\Commands\Integrator\IntegratorCommand;
use Upgrader\Commands\Upgrade\UpgradeCommand;
use Upgrader\Configuration\ConfigurationProvider;
use Upgrader\Lifecycle\Lifecycle;

class UpgraderTask implements TaskInterface
{
    /**
     * @var string
     */
    protected const ID_UPGRADER_TASK = 'upgrader:upgrade';

    /**
     * @var \CodeCompliance\Application\Service\CodeComplianceServiceInterface
     */
    protected CodeComplianceServiceInterface $codeComplianceService;

    /**
     * @var \Upgrader\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var \Codebase\Infrastructure\Service\CodebaseService
     */
    protected CodebaseService $codebaseService;

    /**
     * @var \Upgrade\Application\Services\UpgraderServiceInterface
     */
    protected UpgraderServiceInterface $upgraderService;

    /**
     * @param \CodeCompliance\Application\Service\CodeComplianceServiceInterface $codeComplianceService
     * @param \Upgrader\Configuration\ConfigurationProvider $configurationProvider
     * @param \Codebase\Infrastructure\Service\CodebaseService $codebaseService
     * @param \Upgrade\Application\Services\UpgraderServiceInterface $upgraderService
     */
    public function __construct(
        CodeComplianceServiceInterface $codeComplianceService,
        ConfigurationProvider $configurationProvider,
        CodebaseService $codebaseService,
        UpgraderServiceInterface $upgraderService
    ) {
        $this->codeComplianceService = $codeComplianceService;
        $this->configurationProvider = $configurationProvider;
        $this->codebaseService = $codebaseService;
        $this->upgraderService = $upgraderService;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return static::ID_UPGRADER_TASK;
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return '';
    }

    /**
     * @return array<\SprykerSdk\SdkContracts\Entity\CommandInterface>
     */
    public function getCommands(): array
    {
        return [
            new AnalyzeCommand(
                $this->codeComplianceService,
                $this->configurationProvider,
                $this->codebaseService,
            ),
            new ReportCommand(),
            new UpgradeCommand($this->upgraderService),
            new IntegratorCommand(),
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
        return null;
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
