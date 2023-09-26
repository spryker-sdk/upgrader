<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\PreviousProjectModulesStateStorage;

use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Dto\ProjectModulesStateDto;

interface PreviousProjectModulesStateStorageInterface
{
    /**
     * @param \DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Dto\ProjectModulesStateDto $projectModulesStateDto
     *
     * @return void
     */
    public function setPreviousProjectModulesState(ProjectModulesStateDto $projectModulesStateDto): void;

    /**
     * @return \DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Dto\ProjectModulesStateDto
     */
    public function getRequiredPreviousProjectModulesState(): ProjectModulesStateDto;

    /**
     * @return \DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Dto\ProjectModulesStateDto|null
     */
    public function getPreviousProjectModulesState(): ?ProjectModulesStateDto;
}
