<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Application\Checks\PrivateApi\Used;

use Codebase\Application\Dto\CodebaseSourceDto;
use CodeCompliance\Application\Checks\CodeComplianceCheckInterface;
use CodeCompliance\Domain\Checks\PrivateApi\Used\PersistenceInBusinessModel;
use CodeCompliance\Domain\Entity\Report;

class PersistenceInBusinessModelCheck implements CodeComplianceCheckInterface
{
    /**
     * @var \CodeCompliance\Domain\Checks\PrivateApi\Used\PersistenceInBusinessModel
     */
    protected $persistenceInBusinessModelUsageCheck;

    /**
     * @param \CodeCompliance\Domain\Checks\PrivateApi\Used\PersistenceInBusinessModel $persistenceInBusinessModelUsageCheck
     */
    public function __construct(
        PersistenceInBusinessModel $persistenceInBusinessModelUsageCheck
    ) {
        $this->persistenceInBusinessModelUsageCheck = $persistenceInBusinessModelUsageCheck;
    }

    /**
     * @param \CodeCompliance\Domain\Entity\Report $report
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     *
     * @return \CodeCompliance\Domain\Entity\Report
     */
    public function run(Report $report, CodebaseSourceDto $codebaseSourceDto): Report
    {
        $violations = $this->persistenceInBusinessModelUsageCheck
            ->setCodebaseSourceDto($codebaseSourceDto)
            ->getViolations();

        return $report->addViolations($violations);
    }
}
