<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Application\Service;

use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Infrastructure\Service\CodebaseService;
use CodeCompliance\Domain\Entity\Report;
use SprykerSdk\SdkContracts\Report\Violation\ViolationInterface;
use Upgrader\Configuration\ConfigurationProvider;

class CodeComplianceService implements CodeComplianceServiceInterface
{
    /**
     * @var \Codebase\Infrastructure\Service\CodebaseService
     */
    protected CodebaseService $codebaseService;

    /**
     * @var array<\CodeCompliance\Application\Checks\CodeComplianceCheckInterface>
     */
    protected array $codeComplianceChecks;

    /**
     * @param \Codebase\Infrastructure\Service\CodebaseService $codebaseService
     * @param array<\CodeCompliance\Application\Checks\CodeComplianceCheckInterface> $codeComplianceChecks
     */
    public function __construct(
        CodebaseService $codebaseService,
        array $codeComplianceChecks = []
    ) {
        $this->codebaseService = $codebaseService;
        $this->codeComplianceChecks = $codeComplianceChecks;
    }

    /**
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     *
     * @return \CodeCompliance\Domain\Entity\Report
     */
    public function analyze(CodebaseSourceDto $codebaseSourceDto): Report
    {
        $report = new Report(basename((string)getcwd()), (string)getcwd());

        foreach ($this->codeComplianceChecks as $codeComplianceCheck) {
            $report = $codeComplianceCheck->run($report, $codebaseSourceDto);
        }

        return $report;
    }
}
