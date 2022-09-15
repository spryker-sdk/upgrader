<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Commands\Evaluate\Analyze;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Infrastructure\Service\CodebaseService;
use CodeCompliance\Application\Service\CodeComplianceServiceInterface;
use CodeCompliance\Domain\Entity\Report;
use SprykerSdk\SdkContracts\Entity\ContextInterface;
use SprykerSdk\SdkContracts\Entity\ConverterInterface;
use SprykerSdk\SdkContracts\Entity\ExecutableCommandInterface;
use SprykerSdk\SdkContracts\Violation\ViolationReportableInterface;
use SprykerSdk\SdkContracts\Violation\ViolationReportInterface;
use Upgrader\Configuration\ConfigurationProvider;

class AnalyzeCommand implements ViolationReportableInterface, ExecutableCommandInterface
{
    protected static ?Report $report = null;

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
    public function getCommand(): string
    {
        return static::class;
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
        return [
            'analyze',
            'code compliance',
            'upgrade',
        ];
    }

    /**
     * @return bool
     */
    public function hasStopOnError(): bool
    {
        return false;
    }

    /**
     * @return \SprykerSdk\SdkContracts\Entity\ConverterInterface|null
     */
    public function getConverter(): ?ConverterInterface
    {
        return null;
    }

    /**
     * @return \SprykerSdk\SdkContracts\Violation\ViolationReportInterface|null
     */
    public function getReport(): ?ViolationReportInterface
    {
        return static::$report;
    }

    /**
     * @param \SprykerSdk\SdkContracts\Entity\ContextInterface $context
     *
     * @return \SprykerSdk\SdkContracts\Entity\ContextInterface
     */
    public function execute(ContextInterface $context): ContextInterface
    {
        $codebaseRequestDto = new CodeBaseRequestDto(
            $this->configurationProvider->getToolingConfigurationFilePath(),
            $this->configurationProvider->getSrcPath(),
            $this->configurationProvider->getCorePaths(),
            $this->configurationProvider->getCoreNamespaces(),
            $this->configurationProvider->getIgnoreSources(),
        );

        $codebaseSourceDto = $this->codebaseService->readCodeBase($codebaseRequestDto);

        static::$report = $this->codeComplianceService->analyze($codebaseSourceDto);

        $context->setExitCode(ContextInterface::SUCCESS_EXIT_CODE);

        if (static::$report->hasError()) {
            $context->setExitCode(ContextInterface::FAILURE_EXIT_CODE);
        }

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
