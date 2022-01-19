<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Evaluate\Infrastructure\Event;

use Evaluate\Infrastructure\Service\ReportService;
use Evaluate\Infrastructure\Task\Report\ReportTask;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class ReportListener
{
    /**
     * @var string
     */
    protected const KEY_PRODUCED_BY = 'produced_by';

    /**
     * @var string
     */
    protected const KEY_MESSAGE = 'message';

    /**
     * @var string
     */
    protected const KEY_VIOLATIONS = 'violations';

    /**
     * @var \Evaluate\Infrastructure\Service\ReportService
     */
    protected $reportService;

    /**
     * @param \Evaluate\Infrastructure\Service\ReportService $reportService
     */
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * @param \Symfony\Component\Console\Event\ConsoleCommandEvent $event
     *
     * @return void
     */
    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        if ($event->getCommand() && $event->getCommand()->getName() === ReportTask::ID_REPORT_TASK) {
            $messages = $this->reportService->report();

            if ($messages === []) {
                return;
            }

            $event->getOutput()->writeln((array)$messages);
            $event->getOutput()->writeln('Total messages: ' . count((array)$messages));
        }
    }
}
