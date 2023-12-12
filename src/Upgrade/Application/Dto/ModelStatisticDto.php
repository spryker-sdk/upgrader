<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Dto;

class ModelStatisticDto
{
    /**
     * @var int
     */
    protected int $totalOverwrittenModels = 0;

    /**
     * @var int
     */
    protected int $totalChangedModels = 0;

    /**
     * @var int
     */
    protected int $totalIntersectingModels = 0;

    /**
     * @param int $totalOverwrittenModels
     * @param int $totalChangedModels
     * @param int $totalIntersectingModels
     */
    public function __construct(int $totalOverwrittenModels = 0, int $totalChangedModels = 0, int $totalIntersectingModels = 0)
    {
        $this->totalOverwrittenModels = $totalOverwrittenModels;
        $this->totalChangedModels = $totalChangedModels;
        $this->totalIntersectingModels = $totalIntersectingModels;
    }

    /**
     * @return int
     */
    public function getTotalOverwrittenModels(): int
    {
        return $this->totalOverwrittenModels;
    }

    /**
     * @param int $totalOverwrittenModels
     *
     * @return void
     */
    public function setTotalOverwrittenModels(int $totalOverwrittenModels): void
    {
        $this->totalOverwrittenModels = $totalOverwrittenModels;
    }

    /**
     * @return int
     */
    public function getTotalChangedModels(): int
    {
        return $this->totalChangedModels;
    }

    /**
     * @param int $totalChangedModels
     *
     * @return void
     */
    public function setTotalChangedModels(int $totalChangedModels): void
    {
        $this->totalChangedModels = $totalChangedModels;
    }

    /**
     * @return int
     */
    public function getTotalIntersectingModels(): int
    {
        return $this->totalIntersectingModels;
    }

    /**
     * @param int $totalIntersectingModels
     *
     * @return void
     */
    public function setTotalIntersectingModels(int $totalIntersectingModels): void
    {
        $this->totalIntersectingModels = $totalIntersectingModels;
    }
}
