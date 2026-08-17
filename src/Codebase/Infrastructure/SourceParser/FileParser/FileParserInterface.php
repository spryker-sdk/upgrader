<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Codebase\Infrastructure\SourceParser\FileParser;

use Codebase\Application\Dto\CodebaseSourceDto;
use Symfony\Component\Finder\Finder;

interface FileParserInterface
{
    public function getExtension(): string;

    public function parse(Finder $finder, CodebaseSourceDto $codebaseSourceDto): CodebaseSourceDto;
}
