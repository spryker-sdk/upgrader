<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Console;

use Codebase\Application\Dto\CodeBaseRequestDto;
use Codebase\Infrastructure\Service\CodebaseService;
use CodeCompliance\Application\Service\CodeComplianceServiceInterface;
use CodeCompliance\Domain\Entity\Report;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Upgrader\Configuration\ConfigurationProvider;

class EvaluateConsole extends Command
{
    /**
     * @var string
     */
    protected const NAME = 'evaluate:codebase';

    /**
     * @var string
     */
    protected const DESCRIPTION = 'Evaluates codebase on Paas+ compatibility.';

    /**
     * @var \CodeCompliance\Domain\Entity\Report|null
     */
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
     *
     * @throws LogicException When the command name is empty
     */
    public function __construct(
        CodeComplianceServiceInterface $codeComplianceService,
        ConfigurationProvider $configurationProvider,
        CodebaseService $codebaseService
    ) {
        parent::__construct();
        $this->codeComplianceService = $codeComplianceService;
        $this->configurationProvider = $configurationProvider;
        $this->codebaseService = $codebaseService;
    }

    /**
     * @return void
     */
    protected function configure()
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

        static::$report = $this->codeComplianceService->analyze($codebaseSourceDto);

        if (static::$report->hasError()) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;

    }
}
