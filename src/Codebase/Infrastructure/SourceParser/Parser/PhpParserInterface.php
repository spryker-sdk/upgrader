<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\SourceParser\Parser;

use Codebase\Application\Dto\ClassCodebaseDto;

interface PhpParserInterface extends ParserInterface
{
    /**
     * @param string $namespace
     * @param array<string> $projectPrefixes
     * @param array<string> $coreNamespaces
     * @param \Codebase\Application\Dto\ClassCodebaseDto|null $transfer
     *
     * @return \Codebase\Application\Dto\ClassCodebaseDto|null
     */
    public function parseClass(
        string $namespace,
        array $projectPrefixes,
        array $coreNamespaces = [],
        ?ClassCodebaseDto $transfer = null
    ): ?ClassCodebaseDto;
}
