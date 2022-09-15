<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\Dependency\Parser;

interface CodebaseToParserInterface
{
    /**
     * @param string $dataToParse
     *
     * @return array<\PhpParser\Node\Stmt>|null
     */
    public function parse(string $dataToParse): ?array;
}
