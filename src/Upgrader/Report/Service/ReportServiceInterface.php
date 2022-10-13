<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrader\Report\Service;

use CodeCompliance\Domain\Entity\ReportInterface;

interface ReportServiceInterface
{
    /**
     * @return \CodeCompliance\Domain\Entity\ReportInterface|null
     */
    public function getReport(): ?ReportInterface;

    /**
     * @param \CodeCompliance\Domain\Entity\ReportInterface $report
     *
     * @return void
     */
    public function saveReport(ReportInterface $report): void;

    /**
     * @param \CodeCompliance\Domain\Entity\ReportInterface $report
     * @param bool $isVerbose
     *
     * @return array<string>
     */
    public function generateMessages(ReportInterface $report, bool $isVerbose = false): array;
}
