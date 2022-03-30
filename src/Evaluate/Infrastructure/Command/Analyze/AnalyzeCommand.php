<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Evaluate\Infrastructure\Command\Analyze;

use Codebase\Application\Dto\CodebaseRequestDto;
use Codebase\Application\Dto\ConfigurationRequestDto;
use Codebase\Infrastructure\ProjectConfigurationParser\ProjectConfigurationParserInterface;
use Codebase\Infrastructure\Service\CodebaseService;
use CodeCompliance\Application\Service\CodeComplianceServiceInterface;
use CodeCompliance\Domain\Entity\Report;
use Evaluate\Infrastructure\Configuration\ConfigurationProvider;
use SprykerSdk\SdkContracts\Entity\CommandInterface;
use SprykerSdk\SdkContracts\Entity\ContextInterface;
use SprykerSdk\SdkContracts\Entity\ConverterInterface;
use SprykerSdk\SdkContracts\Entity\ExecutableCommandInterface;
use SprykerSdk\SdkContracts\Violation\ViolationReportableInterface;
use SprykerSdk\SdkContracts\Violation\ViolationReportInterface;

class AnalyzeCommand implements CommandInterface, ViolationReportableInterface, ExecutableCommandInterface
{
    protected static ?Report $report = null;

    /**
     * @var \CodeCompliance\Application\Service\CodeComplianceServiceInterface
     */
    protected CodeComplianceServiceInterface $codeComplianceService;

    /**
     * @var \Codebase\Infrastructure\ProjectConfigurationParser\ProjectConfigurationParserInterface
     */
    protected ProjectConfigurationParserInterface $projectConfigurationParser;

    /**
     * @var \Evaluate\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @var \Codebase\Infrastructure\Service\CodebaseService
     */
    protected CodebaseService $codebaseService;

    /**
     * @param \CodeCompliance\Application\Service\CodeComplianceServiceInterface $codeComplianceService
     * @param \Codebase\Infrastructure\ProjectConfigurationParser\ProjectConfigurationParserInterface $configurationParser
     * @param \Evaluate\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     * @param \Codebase\Infrastructure\Service\CodebaseService $codebaseService
     */
    public function __construct(
        CodeComplianceServiceInterface $codeComplianceService,
        ProjectConfigurationParserInterface $configurationParser,
        ConfigurationProvider $configurationProvider,
        CodebaseService $codebaseService
    ) {
        $this->codeComplianceService = $codeComplianceService;
        $this->projectConfigurationParser = $configurationParser;
        $this->configurationProvider = $configurationProvider;
        $this->codebaseService = $codebaseService;
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
    public function getViolationConverter(): ?ConverterInterface
    {
        return null;
    }

    /**
     * @return \SprykerSdk\SdkContracts\Violation\ViolationReportInterface|null
     */
    public function getViolationReport(): ?ViolationReportInterface
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
        $projectConfigurationRequest = new ConfigurationRequestDto(
            $this->configurationProvider->getProjectConfigurationFilePath(),
            $this->configurationProvider->getSrcDirectory(),
        );
        $projectConfiguration = $this->projectConfigurationParser->parseConfiguration($projectConfigurationRequest);
        
        $codebaseRequestDto = new CodebaseRequestDto(
            $projectConfiguration->getProjectDirectories(),
            $this->configurationProvider->getCoreDirectory(),
            $this->configurationProvider->getCoreNamespaces(),
            $projectConfiguration->getProjectPrefixes(),
            $this->configurationProvider->getIgnoreSources(),
        );

        $codebaseSourceDto = $this->codebaseService->parseSource($codebaseRequestDto);

        static::$report = $this->codeComplianceService->analyze($codebaseSourceDto);

        $context->setExitCode(ContextInterface::SUCCESS_EXIT_CODE);

        if (static::$report->hasError()) {
            $context->setExitCode(ContextInterface::FAILURE_EXIT_CODE);
        }

        return $context;
    }
}
