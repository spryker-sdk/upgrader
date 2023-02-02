<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\Step;

use Upgrade\Application\Adapter\VersionControlSystemAdapterInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Report\ReportSendProcessorInterface;
use Upgrade\Application\Strategy\StepInterface;

class SendReportStep extends AbstractStep implements StepInterface
{
    /**
     * @var \Upgrade\Application\Report\ReportSendProcessorInterface
     */
    protected ReportSendProcessorInterface $reportSendProcessor;

    /**
     * @param \Upgrade\Application\Report\ReportSendProcessorInterface $reportSendProcessor
     * @param \Upgrade\Application\Adapter\VersionControlSystemAdapterInterface $versionControlSystem
     */
    public function __construct(ReportSendProcessorInterface $reportSendProcessor, VersionControlSystemAdapterInterface $versionControlSystem)
    {
        parent::__construct($versionControlSystem);

        $this->reportSendProcessor = $reportSendProcessor;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->reportSendProcessor->process($stepsExecutionDto);
    }
}
