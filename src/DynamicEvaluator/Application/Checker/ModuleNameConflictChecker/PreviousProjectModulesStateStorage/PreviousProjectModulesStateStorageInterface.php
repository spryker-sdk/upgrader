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
    public function setPreviousProjectModulesState(ProjectModulesStateDto $projectModulesStateDto): void;

    public function getRequiredPreviousProjectModulesState(): ProjectModulesStateDto;

    public function getPreviousProjectModulesState(): ?ProjectModulesStateDto;
}
