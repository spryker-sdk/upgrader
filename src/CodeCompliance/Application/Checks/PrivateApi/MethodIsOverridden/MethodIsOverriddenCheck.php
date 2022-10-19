<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Application\Checks\PrivateApi\MethodIsOverridden;

use Codebase\Application\Dto\CodebaseSourceDto;
use CodeCompliance\Application\Checks\CodeComplianceCheckInterface;
use CodeCompliance\Domain\Checks\PrivateApi\MethodIsOverridden\MethodIsOverridden;
use CodeCompliance\Domain\Entity\Report;

class MethodIsOverriddenCheck implements CodeComplianceCheckInterface
{
    /**
     * @var \CodeCompliance\Domain\Checks\PrivateApi\MethodIsOverridden\MethodIsOverridden
     */
    protected $methodIsOverriddenCheck;

    /**
     * @param \CodeCompliance\Domain\Checks\PrivateApi\MethodIsOverridden\MethodIsOverridden $methodIsOverriddenCheck
     */
    public function __construct(MethodIsOverridden $methodIsOverriddenCheck)
    {
        $this->methodIsOverriddenCheck = $methodIsOverriddenCheck;
    }

    /**
     * @param \CodeCompliance\Domain\Entity\Report $report
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     *
     * @return \CodeCompliance\Domain\Entity\Report
     */
    public function run(Report $report, CodebaseSourceDto $codebaseSourceDto): Report
    {
        $violations = $this->methodIsOverriddenCheck
            ->setCodebaseSourceDto($codebaseSourceDto)
            ->getViolations();

        return $report->addViolations($violations);
    }
}
