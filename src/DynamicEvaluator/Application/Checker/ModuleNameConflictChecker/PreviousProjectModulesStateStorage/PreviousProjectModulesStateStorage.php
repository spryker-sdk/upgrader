<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\PreviousProjectModulesStateStorage;

use DynamicEvaluator\Application\Checker\ModuleNameConflictChecker\Dto\ProjectModulesStateDto;
use InvalidArgumentException;

class PreviousProjectModulesStateStorage implements PreviousProjectModulesStateStorageInterface
{
    protected ?ProjectModulesStateDto $previousProjectModulesStateDto = null;

    public function setPreviousProjectModulesState(ProjectModulesStateDto $projectModulesStateDto): void
    {
        $this->previousProjectModulesStateDto = $projectModulesStateDto;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getRequiredPreviousProjectModulesState(): ProjectModulesStateDto
    {
        if ($this->previousProjectModulesStateDto === null) {
            throw new InvalidArgumentException('Previous state should be set first');
        }

        return $this->previousProjectModulesStateDto;
    }

    public function getPreviousProjectModulesState(): ?ProjectModulesStateDto
    {
        return $this->previousProjectModulesStateDto;
    }
}
