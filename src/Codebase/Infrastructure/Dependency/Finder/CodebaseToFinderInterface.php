<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Codebase\Infrastructure\Dependency\Finder;

use Symfony\Component\Finder\Finder;

interface CodebaseToFinderInterface
{
    /**
     * @param array<string> $extensions
     * @param array<string> $paths
     * @param array<string> $exclude
     *
     * @return \Symfony\Component\Finder\Finder
     */
    public function findSourceByExtension(array $extensions, array $paths, array $exclude = []): Finder;
}
