<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Service;

use Codebase\Application\Dto\ClassCodebaseDto;

interface CodeBaseServiceInterface
{
    /**
     * @param string $classNamespace
     * @param array<string> $projectPrefixes
     * @param array<string> $coreNamespaces
     *
     * @return \Codebase\Application\Dto\ClassCodebaseDto|null
     */
    public function parsePhpClass(string $classNamespace, array $projectPrefixes, array $coreNamespaces = []): ?ClassCodebaseDto;
}
