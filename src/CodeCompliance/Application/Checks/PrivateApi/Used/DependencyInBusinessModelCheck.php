<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Application\Checks\PrivateApi\Used;

use Codebase\Application\Dto\CodebaseSourceDto;
use CodeCompliance\Application\Checks\CodeComplianceCheckInterface;
use CodeCompliance\Domain\Checks\PrivateApi\Used\DependencyInBusinessModel;
use CodeCompliance\Domain\Entity\Report;

class DependencyInBusinessModelCheck implements CodeComplianceCheckInterface
{
    /**
     * @var \CodeCompliance\Domain\Checks\PrivateApi\Used\DependencyInBusinessModel
     */
    protected $dependencyInBusinessModelUsageCheck;

    /**
     * @param \CodeCompliance\Domain\Checks\PrivateApi\Used\DependencyInBusinessModel $dependencyInBusinessModelUsageCheck
     */
    public function __construct(
        DependencyInBusinessModel $dependencyInBusinessModelUsageCheck
    ) {
        $this->dependencyInBusinessModelUsageCheck = $dependencyInBusinessModelUsageCheck;
    }

    /**
     * @param \CodeCompliance\Domain\Entity\Report $report
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     *
     * @return \CodeCompliance\Domain\Entity\Report
     */
    public function run(Report $report, CodebaseSourceDto $codebaseSourceDto): Report
    {
        $violations = $this->dependencyInBusinessModelUsageCheck
            ->setCodebaseSourceDto($codebaseSourceDto)
            ->getViolations();

        return $report->addViolations($violations);
    }
}
