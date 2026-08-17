<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Dto;

class ModelStatisticDto
{
    protected int $totalOverwrittenModels = 0;

    protected int $totalChangedModels = 0;

    protected int $totalIntersectingModels = 0;

    /**
     * @var array<string>
     */
    protected array $intersectingModules = [];

    /**
     * @param array<string> $intersectingModules
     */
    public function __construct(int $totalOverwrittenModels = 0, int $totalChangedModels = 0, int $totalIntersectingModels = 0, array $intersectingModules = [])
    {
        $this->totalOverwrittenModels = $totalOverwrittenModels;
        $this->totalChangedModels = $totalChangedModels;
        $this->totalIntersectingModels = $totalIntersectingModels;
        $this->intersectingModules = $intersectingModules;
    }

    public function getTotalOverwrittenModels(): int
    {
        return $this->totalOverwrittenModels;
    }

    public function setTotalOverwrittenModels(int $totalOverwrittenModels): void
    {
        $this->totalOverwrittenModels = $totalOverwrittenModels;
    }

    public function getTotalChangedModels(): int
    {
        return $this->totalChangedModels;
    }

    public function setTotalChangedModels(int $totalChangedModels): void
    {
        $this->totalChangedModels = $totalChangedModels;
    }

    public function getTotalIntersectingModels(): int
    {
        return $this->totalIntersectingModels;
    }

    public function setTotalIntersectingModels(int $totalIntersectingModels): void
    {
        $this->totalIntersectingModels = $totalIntersectingModels;
    }

    /**
     * @return array<string>
     */
    public function getIntersectingModules(): array
    {
        return $this->intersectingModules;
    }

    /**
     * @param array<string> $intersectingModules
     */
    public function setIntersectingModules(array $intersectingModules): void
    {
        $this->intersectingModules = $intersectingModules;
    }
}
