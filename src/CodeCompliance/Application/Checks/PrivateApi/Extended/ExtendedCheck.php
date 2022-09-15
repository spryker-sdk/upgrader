<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Application\Checks\PrivateApi\Extended;

use Codebase\Application\Dto\CodebaseSourceDto;
use CodeCompliance\Application\Checks\CodeComplianceCheckInterface;
use CodeCompliance\Domain\Checks\PrivateApi\Extended\Extended;
use CodeCompliance\Domain\Entity\Report;

class ExtendedCheck implements CodeComplianceCheckInterface
{
    /**
     * @var \CodeCompliance\Domain\Checks\PrivateApi\Extended\Extended
     */
    protected $extensionCheck;

    /**
     * @param \CodeCompliance\Domain\Checks\PrivateApi\Extended\Extended $extensionCheck
     */
    public function __construct(Extended $extensionCheck)
    {
        $this->extensionCheck = $extensionCheck;
    }

    /**
     * @param \CodeCompliance\Domain\Entity\Report $report
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     *
     * @return \CodeCompliance\Domain\Entity\Report
     */
    public function run(Report $report, CodebaseSourceDto $codebaseSourceDto): Report
    {
        $violations = $this->extensionCheck
            ->setCodebaseSourceDto($codebaseSourceDto)
            ->getViolations();

        return $report->addViolations($violations);
    }
}
