<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Console;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Application\Dto\ModuleDto;
use Codebase\Infrastructure\Service\CodebaseService;
use CodeCompliance\Application\Service\CodeComplianceServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Upgrade\Application\Exception\UpgraderException;
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
     * @var string
     */
    protected const OPTION_MODULE = 'module';

    /**
     * @var string
     */
    protected const OPTION_MODULES_SHORT = '-m';

    /**
     * @var int
     */
    protected const MODULE_NAMESPACE_INDEX = 0;

    /**
     * @var int
     */
    protected const MODULE_NAME_INDEX = 1;

    /**
     * @var string
     */
    public const MODULE_SEPARATOR = ' ';

    /**
     * @var string
     */
    public const NAMESPACE_NAME_SEPARATOR = '.';

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
    protected function configure()
    {
        parent::configure();

        $this->setName(static::NAME);
        $this->setDescription(static::DESCRIPTION);
        $this->addOption(static::OPTION_MODULE, static::OPTION_MODULES_SHORT, InputArgument::OPTIONAL);
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
            $this->getModuleList($input),
        );

        $codebaseSourceDto = $this->codebaseService->readCodeBase($codebaseRequestDto);

        $report = $this->codeComplianceService->analyze($codebaseSourceDto);

        if ($report->hasError()) {
            $this->reportService->save($report);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return array<\Codebase\Application\Dto\ModuleDto>
     */
    protected function getModuleList(InputInterface $input): array
    {
        $modules = [];
        $moduleOption = (string)$input->getOption(static::OPTION_MODULE);

        if (!$moduleOption) {
            return $modules;
        }

        foreach (explode(self::MODULE_SEPARATOR, $moduleOption) as $namespaceName) {
            $moduleData = explode(self::NAMESPACE_NAME_SEPARATOR, $namespaceName);

            if (!isset($moduleData[self::MODULE_NAME_INDEX])) {
                throw new UpgraderException('Please specify module with namespace {Namespace}.{ModuleName}. Example: Pyz.DataImport');
            }

            $modules[] = new ModuleDto($moduleData[self::MODULE_NAMESPACE_INDEX], $moduleData[self::MODULE_NAME_INDEX]);
        }

        return $modules;
    }
}
