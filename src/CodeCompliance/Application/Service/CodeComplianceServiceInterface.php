<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Application\Service;

use Codebase\Application\Dto\CodebaseSourceDto;
use CodeCompliance\Domain\Entity\Report;

interface CodeComplianceServiceInterface
{
    /**
     * @param \Codebase\Application\Dto\CodebaseSourceDto $codebaseSourceDto
     *
     * @return \CodeCompliance\Domain\Entity\Report
     */
    public function analyze(CodebaseSourceDto $codebaseSourceDto): Report;
}
