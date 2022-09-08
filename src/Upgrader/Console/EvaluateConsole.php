<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrader\Console;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Infrastructure\Service\CodebaseService;
use CodeCompliance\Application\Service\CodeComplianceServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Upgrader\Configuration\ConfigurationProvider;
use Upgrader\Report\Service\ReportService;

class EvaluateConsole extends Command
{
    /**
     * @var string
     */
    protected const NAME = 'analyze:php:code-compliance';

    /**
     * @var string
     */
    protected const DESCRIPTION = 'Analyze codebase on Paas+ compatibility.';

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
     * @var \Upgrader\Report\Service\ReportService
     */
    protected ReportService $reportService;

    /**
     * @param \CodeCompliance\Application\Service\CodeComplianceServiceInterface $codeComplianceService
     * @param \Upgrader\Configuration\ConfigurationProvider $configurationProvider
     * @param \Codebase\Infrastructure\Service\CodebaseService $codebaseService
     * @param \Upgrader\Report\Service\ReportService $reportService
     */
    public function __construct(
        CodeComplianceServiceInterface $codeComplianceService,
        ConfigurationProvider $configurationProvider,
        CodebaseService $codebaseService,
        ReportService $reportService
    ) {
        parent::__construct();
        $this->codeComplianceService = $codeComplianceService;
        $this->configurationProvider = $configurationProvider;
        $this->codebaseService = $codebaseService;
        $this->reportService = $reportService;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName(static::NAME);
        $this->setDescription(static::DESCRIPTION);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $codebaseRequestDto = new CodeBaseRequestDto(
            $this->configurationProvider->getToolingConfigurationFilePath(),
            $this->configurationProvider->getSrcPath(),
            $this->configurationProvider->getCorePaths(),
            $this->configurationProvider->getCoreNamespaces(),
            $this->configurationProvider->getIgnoreSources(),
        );

        $codebaseSourceDto = $this->codebaseService->readCodeBase($codebaseRequestDto);

        $report = $this->codeComplianceService->analyze($codebaseSourceDto);

        if ($report->hasError()) {
            $this->reportService->save($report);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
