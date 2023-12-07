<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\BrokenPhpFilesChecker\SprykerModule;

class SprykerModulesStateStorage
{
    /**
     * @var array<string, string> <module-name>: <version>
     */
    protected array $modulesState = [];

    /**
     * @param array<string, string> $modulesState <module-name>: <version>
     */
    public function __construct(array $modulesState = [])
    {
        $this->modulesState = $modulesState;
    }

    /**
     * @return array<string, string> <module-name>: <version>
     */
    public function getModulesState(): array
    {
        return $this->modulesState;
    }

    /**
     * @param array<string, string> $modulesState <module-name>: <version>
     *
     * @return void
     */
    public function setModulesState(array $modulesState): void
    {
        $this->modulesState = $modulesState;
    }
}
