<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Tasks\Evaluate\Analyze;

use Codebase\Infrastructure\Service\CodebaseService;
use CodeCompliance\Application\Service\CodeComplianceServiceInterface;
use SprykerSdk\SdkContracts\Entity\Lifecycle\LifecycleInterface;
use SprykerSdk\SdkContracts\Entity\TaskInterface;
use Upgrader\Commands\Evaluate\Analyze\AnalyzeCommand;
use Upgrader\Configuration\ConfigurationProvider;
use Upgrader\Lifecycle\Lifecycle;

class AnalyzeTask implements TaskInterface
{
    /**
     * @var string
     */
    public const ID_ANALYZE_TASK = 'analyze:php:code-compliance';

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
     * @param \CodeCompliance\Application\Service\CodeComplianceServiceInterface $codeComplianceService
     * @param \Upgrader\Configuration\ConfigurationProvider $configurationProvider
     * @param \Codebase\Infrastructure\Service\CodebaseService $codebaseService
     */
    public function __construct(
        CodeComplianceServiceInterface $codeComplianceService,
        ConfigurationProvider $configurationProvider,
        CodebaseService $codebaseService
    ) {
        $this->codeComplianceService = $codeComplianceService;
        $this->configurationProvider = $configurationProvider;
        $this->codebaseService = $codebaseService;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return 'analyze:php:code-compliance';
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return 'Analyzes project code for PaaS+ compliance.';
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
        return 'Checks project code for code compliance before automatic update.';
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
