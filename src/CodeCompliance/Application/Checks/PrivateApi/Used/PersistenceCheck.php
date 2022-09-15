<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Application\Checks\PrivateApi\Used;

use Codebase\Application\Dto\CodebaseSourceDto;
use CodeCompliance\Application\Checks\CodeComplianceCheckInterface;
use CodeCompliance\Domain\Checks\PrivateApi\Used\Persistence;
use CodeCompliance\Domain\Entity\Report;

class PersistenceCheck implements CodeComplianceCheckInterface
{
    /**
     * @var \CodeCompliance\Domain\Checks\PrivateApi\Used\Persistence
     */
    protected $persistenceUsageCheck;

    /**
     * @param \CodeCompliance\Domain\Checks\PrivateApi\Used\Persistence $persistenceUsageCheck
     */
    public function __construct(Persistence $persistenceUsageCheck)
    {
        $this->persistenceUsageCheck = $persistenceUsageCheck;
    }

    /**
     * @param \CodeCompliance\Domain\Entity\Report $report
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     *
     * @return \CodeCompliance\Domain\Entity\Report
     */
    public function run(Report $report, CodebaseSourceDto $codebaseSourceDto): Report
    {
        $violations = $this->persistenceUsageCheck
            ->setCodebaseSourceDto($codebaseSourceDto)
            ->getViolations();

        return $report->addViolations($violations);
    }
}
