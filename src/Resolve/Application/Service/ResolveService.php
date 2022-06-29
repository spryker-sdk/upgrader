<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Application\Service;

use Codebase\Application\Dto\CodebaseSourceDto;
use Codebase\Infrastructure\Service\CodebaseService;
use Resolve\Application\Checks\ResolveCheckInterface;
use Resolve\Domain\Entity\Message;

class ResolveService implements ResolveServiceInterface
{
    /**
     * @var CodebaseService
     */
    protected CodebaseService $codebaseService;

    /**
     * @var array<ResolveCheckInterface>
     */
    protected array $resolveChecks;

    /**
     * @param CodebaseService $codebaseService
     * @param array<ResolveCheckInterface> resolveChecks
     */
    public function __construct(
        CodebaseService $codebaseService,
        array $resolveChecks = []
    ) {
        $this->codebaseService = $codebaseService;
        $this->resolveChecks = $resolveChecks;
    }

    /**
     * @param CodebaseSourceDto $codebaseSourceDto
     *
     * @return Message
     */
    public function resolve(CodebaseSourceDto $codebaseSourceDto): Message
    {
        return 'test';
        /*$report = new Report('test', (string)getcwd());

        foreach ($this->codeComplianceChecks as $codeComplianceCheck) {
            $report = $codeComplianceCheck->run($report, $codebaseSourceDto);
        }

        return $report;*/
    }
}
