<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Dto;

class ProjectModulesStateDto
{
    /**
     * @var array<string>
     */
    protected array $projectModules;

    /**
     * @var array<string>
     */
    protected array $composerInstalledModules;

    /**
     * @param array<string> $projectModules
     * @param array<string> $composerInstalledModules
     */
    public function __construct(array $projectModules, array $composerInstalledModules)
    {
        $this->projectModules = $projectModules;
        $this->composerInstalledModules = $composerInstalledModules;
    }

    /**
     * @return array<string>
     */
    public function getProjectModules(): array
    {
        return $this->projectModules;
    }

    /**
     * @return array<string>
     */
    public function getComposerInstalledModules(): array
    {
        return $this->composerInstalledModules;
    }
}
