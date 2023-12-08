<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule;

interface SprykerModuleComparerInterface
{
    /**
     * @param array<string, string> $previousSprykerModules array{ name: version }
     * @param array<string, string> $newSprykerModules array{ name: version }
     *
     * @return array<string>
     */
    public function compareForUpdatedModules(array $previousSprykerModules, array $newSprykerModules): array;
}
