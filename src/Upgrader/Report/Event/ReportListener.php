<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Report\Event;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Upgrader\Report\Service\ReportService;
use Upgrader\Tasks\Evaluate\Analyze\AnalyzeTask;
use Upgrader\Tasks\Evaluate\Report\ReportTask;

class ReportListener
{
    /**
     * @var string
     */
    protected const KEY_VIOLATIONS = 'violations';

    /**
     * @var \Upgrader\Report\Service\ReportService
     */
    protected $reportService;

    /**
     * @param \Upgrader\Report\Service\ReportService $reportService
     */
    public function __construct(ReportService $reportService)
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
            (
                $event->getCommand()->getName() === ReportTask::ID_REPORT_TASK ||
                $event->getCommand()->getName() === AnalyzeTask::ID_ANALYZE_TASK
            )
        ) {
            $messages = $this->reportService->report();

            if (!$messages) {
                return;
            }

            $event->getOutput()->writeln((array)$messages);
            $event->getOutput()->writeln('Total messages: ' . count((array)$messages));
            $event->setExitCode(Command::FAILURE);
        }
    }
}
