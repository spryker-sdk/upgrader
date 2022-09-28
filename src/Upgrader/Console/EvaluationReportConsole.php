<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrader\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Upgrader\Report\Service\ReportService;

class EvaluationReportConsole extends Command
{
    /**
     * @var string
     */
    protected const NAME = 'analyze:php:code-compliance-report';

    /**
     * @var string
     */
    protected const DESCRIPTION = 'Output evaluation report.';

    /**
     * @var \Upgrader\Report\Service\ReportService
     */
    protected ReportService $reportService;

    /**
     * @param \Upgrader\Report\Service\ReportService $reportService
     */
    public function __construct(ReportService $reportService)
    {
        parent::__construct();
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
        $messages = $this->reportService->report($output->isVerbose());

        if ($messages === []) {
            return Command::SUCCESS;
        }

        $output->writeln((array)$messages);
        $output->writeln('Total messages: ' . count((array)$messages));

        return Command::SUCCESS;
    }
}
