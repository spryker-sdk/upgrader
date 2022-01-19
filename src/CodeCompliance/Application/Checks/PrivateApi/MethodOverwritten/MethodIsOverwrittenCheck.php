<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Application\Checks\PrivateApi\MethodOverwritten;

use Codebase\Application\Dto\CodebaseSourceDto;
use CodeCompliance\Application\Checks\CodeComplianceCheckInterface;
use CodeCompliance\Domain\Checks\PrivateApi\MethodOverwritten\MethodIsOverwritten;
use CodeCompliance\Domain\Entity\Report;

class MethodIsOverwrittenCheck implements CodeComplianceCheckInterface
{
    /**
     * @var \CodeCompliance\Domain\Checks\PrivateApi\MethodOverwritten\MethodIsOverwritten
     */
    protected $methodIsOverwrittenCheck;

    /**
     * @param \CodeCompliance\Domain\Checks\PrivateApi\MethodOverwritten\MethodIsOverwritten $methodIsOverwrittenCheck
     */
    public function __construct(
        MethodIsOverwritten $methodIsOverwrittenCheck
    ) {
        $this->methodIsOverwrittenCheck = $methodIsOverwrittenCheck;
    }

    /**
     * @param \CodeCompliance\Domain\Entity\Report $report
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     *
     * @return \CodeCompliance\Domain\Entity\Report
     */
    public function run(Report $report, CodebaseSourceDto $codebaseSourceDto): Report
    {
        $violations = $this->methodIsOverwrittenCheck
            ->setCodebaseSourceDto($codebaseSourceDto)
            ->getViolations();

        return $report->addViolations($violations);
    }
}
