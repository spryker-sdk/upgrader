<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\Step;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Infrastructure\Service\CodebaseService;
use CodeCompliance\Application\Service\CodeComplianceServiceInterface;
use Upgrade\Application\Adapter\VersionControlSystemAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\StepInterface;
use Upgrader\Configuration\ConfigurationProvider;

class CodeComplianceStep extends AbstractStep implements StepInterface
{
    /**
     * @var \CodeCompliance\Application\Service\CodeComplianceServiceInterface
     */
    protected CodeComplianceServiceInterface $codeComplianceService;

    /**
     * @var \Upgrader\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $codeComplianceConfigurationProvider;

    /**
     * @var \Codebase\Infrastructure\Service\CodebaseService
     */
    protected CodebaseService $codebaseService;

    /**
     * @var \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $upgradeConfigurationProvider;

    /**
     * @param \Upgrade\Application\Adapter\VersionControlSystemAdapterInterface $versionControlSystem
     * @param \CodeCompliance\Application\Service\CodeComplianceServiceInterface $codeComplianceService
     * @param \Upgrader\Configuration\ConfigurationProvider $codeComplianceConfigurationProvider
     * @param \Codebase\Infrastructure\Service\CodebaseService $codebaseService
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $upgradeConfigurationProvider
     */
    public function __construct(
        VersionControlSystemAdapterInterface $versionControlSystem,
        CodeComplianceServiceInterface $codeComplianceService,
        ConfigurationProvider $codeComplianceConfigurationProvider,
        CodebaseService $codebaseService,
        ConfigurationProviderInterface $upgradeConfigurationProvider
    ) {
        parent::__construct($versionControlSystem);
        $this->codeComplianceService = $codeComplianceService;
        $this->codeComplianceConfigurationProvider = $codeComplianceConfigurationProvider;
        $this->codebaseService = $codebaseService;
        $this->upgradeConfigurationProvider = $upgradeConfigurationProvider;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        if (!$this->upgradeConfigurationProvider->isEvaluatorEnabled()) {
            return $stepsExecutionDto;
        }

        $codebaseRequestDto = new CodeBaseRequestDto(
            $this->codeComplianceConfigurationProvider->getToolingConfigurationFilePath(),
            $this->codeComplianceConfigurationProvider->getSrcPath(),
            $this->codeComplianceConfigurationProvider->getCorePaths(),
            $this->codeComplianceConfigurationProvider->getCoreNamespaces(),
            $this->codeComplianceConfigurationProvider->getIgnoreSources(),
        );

        $codebaseSourceDto = $this->codebaseService->readCodeBase($codebaseRequestDto);
        $report = $this->codeComplianceService->analyze($codebaseSourceDto);

        $stepsExecutionDto->setCodeComplianceReport($report);

        return $stepsExecutionDto;
    }
}
