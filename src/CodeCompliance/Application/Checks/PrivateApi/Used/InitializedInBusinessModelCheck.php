<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Application\Checks\PrivateApi\Used;

use Codebase\Application\Dto\CodebaseSourceDto;
use CodeCompliance\Application\Checks\CodeComplianceCheckInterface;
use CodeCompliance\Domain\Checks\PrivateApi\Used\ObjectIsInitializedInBusinessModel;
use CodeCompliance\Domain\Entity\Report;

class InitializedInBusinessModelCheck implements CodeComplianceCheckInterface
{
    /**
     * @var \CodeCompliance\Domain\Checks\PrivateApi\Used\ObjectIsInitializedInBusinessModel
     */
    protected $objectInitializationCheck;

    /**
     * @param \CodeCompliance\Domain\Checks\PrivateApi\Used\ObjectIsInitializedInBusinessModel $objectInitializationCheck
     */
    public function __construct(
        ObjectIsInitializedInBusinessModel $objectInitializationCheck
    ) {
        $this->objectInitializationCheck = $objectInitializationCheck;
    }

    /**
     * @param \CodeCompliance\Domain\Entity\Report $report
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     *
     * @return \CodeCompliance\Domain\Entity\Report
     */
    public function run(Report $report, CodebaseSourceDto $codebaseSourceDto): Report
    {
        $violations = $this->objectInitializationCheck
            ->setCodebaseSourceDto($codebaseSourceDto)
            ->getViolations();

        return $report->addViolations($violations);
    }
}
