<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Application\Checks\NotUnique;

use Codebase\Application\Dto\CodebaseSourceDto;
use CodeCompliance\Application\Checks\CodeComplianceCheckInterface;
use CodeCompliance\Domain\Checks\NotUnique\TransferProperty;
use CodeCompliance\Domain\Entity\Report;

class TransferPropertyCheck implements CodeComplianceCheckInterface
{
    /**
     * @var \CodeCompliance\Domain\Checks\NotUnique\TransferProperty
     */
    protected TransferProperty $transferPropertyCheck;

    /**
     * @param \CodeCompliance\Domain\Checks\NotUnique\TransferProperty $transferPropertyCheck
     */
    public function __construct(TransferProperty $transferPropertyCheck)
    {
        $this->transferPropertyCheck = $transferPropertyCheck;
    }

    /**
     * @param \CodeCompliance\Domain\Entity\Report $report
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     *
     * @return \CodeCompliance\Domain\Entity\Report
     */
    public function run(Report $report, CodebaseSourceDto $codebaseSourceDto): Report
    {
        $violations = $this->transferPropertyCheck
            ->setCodebaseSourceDto($codebaseSourceDto)
            ->getViolations();

        return $report->addViolations($violations);
    }
}
