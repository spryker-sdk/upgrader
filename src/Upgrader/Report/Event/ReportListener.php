<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrader\Report\Event;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Upgrader\Report\Service\ReportServiceInterface;
use Upgrader\Tasks\Evaluate\Analyze\AnalyzeTask;
use Upgrader\Tasks\Evaluate\Report\ReportTask;

class ReportListener
{
    /**
     * @var \Upgrader\Report\Service\ReportServiceInterface
     */
    protected $reportService;

    /**
     * @param \Upgrader\Report\Service\ReportServiceInterface $reportService
     */
    public function __construct(ReportServiceInterface $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * @param \Symfony\Component\Console\Event\ConsoleTerminateEvent $event
     *
     * @return void
     */
    public function onConsoleCommandTerminate(ConsoleTerminateEvent $event): void
    {
        if (
            $event->getCommand() &&
            $event->getCommand()->getName() !== AnalyzeTask::ID_ANALYZE_TASK &&
            $event->getCommand()->getName() !== ReportTask::ID_REPORT_TASK
        ) {
            return;
        }

        $report = $this->reportService->getReport();
        if (!$report) {
            $event->setExitCode(Command::SUCCESS);

            return;
        }

        $messages = $this->reportService->generateMessages($report, $event->getOutput()->isVerbose());
        $event->getOutput()->writeln($messages);

        if ($report->hasError()) {
            $event->setExitCode(Command::FAILURE);

            return;
        }

        $event->setExitCode(Command::SUCCESS);
    }
}
