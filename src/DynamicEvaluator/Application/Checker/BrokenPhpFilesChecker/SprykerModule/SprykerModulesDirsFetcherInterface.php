<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule;

interface SprykerModulesDirsFetcherInterface
{
    /**
     * @param array<string> $sprykerPackages List of modules
     *
     * @return array<string> List of local modules dirs
     */
    public function fetchModulesDirs(array $sprykerPackages): array;
}
