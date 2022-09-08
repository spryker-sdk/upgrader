<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Application\Checks\NotUnique;

use Codebase\Application\Dto\CodebaseSourceDto;
use CodeCompliance\Application\Checks\CodeComplianceCheckInterface;
use CodeCompliance\Domain\Checks\NotUnique\TransferName;
use CodeCompliance\Domain\Entity\Report;

class TransferNameCheck implements CodeComplianceCheckInterface
{
    /**
     * @var \CodeCompliance\Domain\Checks\NotUnique\TransferName
     */
    protected TransferName $transferNameCheck;

    /**
     * @param \CodeCompliance\Domain\Checks\NotUnique\TransferName $transferNameCheck
     */
    public function __construct(TransferName $transferNameCheck)
    {
        $this->transferNameCheck = $transferNameCheck;
    }

    /**
     * @param \CodeCompliance\Domain\Entity\Report $report
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     *
     * @return \CodeCompliance\Domain\Entity\Report
     */
    public function run(Report $report, CodebaseSourceDto $codebaseSourceDto): Report
    {
        $violations = $this->transferNameCheck
            ->setCodebaseSourceDto($codebaseSourceDto)
            ->getViolations();

        return $report->addViolations($violations);
    }
}
