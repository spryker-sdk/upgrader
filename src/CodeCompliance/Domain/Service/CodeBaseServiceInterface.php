<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace CodeCompliance\Domain\Service;

use Codebase\Application\Dto\ClassCodebaseDto;
use Codebase\Application\Dto\ConfigurationResponseDto;

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

    /**
     * @return \Codebase\Application\Dto\ConfigurationResponseDto
     */
    public function readToolingConfiguration(): ConfigurationResponseDto;
}
