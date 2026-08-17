<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Codebase\Infrastructure\SourceParser\FileParser;

use Codebase\Application\Dto\ClassCodebaseDto;

interface PhpFileParserInterface extends FileParserInterface
{
    /**
     * @param array<string> $projectPrefixes
     * @param array<string> $coreNamespaces
     */
    public function parseClass(
        string $namespace,
        array $projectPrefixes,
        array $coreNamespaces = [],
        ?ClassCodebaseDto $transfer = null
    ): ?ClassCodebaseDto;
}
