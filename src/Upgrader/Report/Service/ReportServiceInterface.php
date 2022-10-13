<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrader\Report\Service;

use CodeCompliance\Domain\Entity\ReportInterface;
use CodeCompliance\Domain\Entity\ViolationInterface;

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
    public function save(ReportInterface $report): void;

    /**
     * @param \CodeCompliance\Domain\Entity\ViolationInterface $violation
     * @param bool $isVerbose
     *
     * @return string
     */
    public function generateMessage(ViolationInterface $violation, bool $isVerbose = false): string;
}
