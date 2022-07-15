<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Application\Checks\NotUnique;

use Codebase\Application\Dto\CodebaseSourceDto;
use CodeCompliance\Application\Checks\CodeComplianceCheckInterface;
use CodeCompliance\Domain\Checks\NotUnique\ModuleName;
use CodeCompliance\Domain\Entity\Report;

class ModuleNameCheck implements CodeComplianceCheckInterface
{
    /**
     * @var \CodeCompliance\Domain\Checks\NotUnique\ModuleName
     */
    protected ModuleName $moduleNameCheck;

    /**
     * @param \CodeCompliance\Domain\Checks\NotUnique\ModuleName $moduleName
     */
    public function __construct(ModuleName $moduleName)
    {
        $this->moduleNameCheck = $moduleName;
    }

    /**
     * @param \CodeCompliance\Domain\Entity\Report $report
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     *
     * @return \CodeCompliance\Domain\Entity\Report
     */
    public function run(Report $report, CodebaseSourceDto $codebaseSourceDto): Report
    {
        $violations = $this->moduleNameCheck
            ->setCodebaseSourceDto($codebaseSourceDto)
            ->getViolations();

        return $report->addViolations($violations);
    }
}
