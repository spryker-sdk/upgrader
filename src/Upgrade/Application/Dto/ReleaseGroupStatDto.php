<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Dto;

class ReleaseGroupStatDto
{
    /**
     * @var int
     */
    protected int $availableRgsAmount = 0;

    /**
     * @var int
     */
    protected int $appliedPackagesAmount = 0;

    /**
     * @var int
     */
    protected int $appliedRGsAmount = 0;

    /**
     * @var int
     */
    protected int $appliedSecurityFixesAmount = 0;

    /**
     * @return int
     */
    public function getAvailableRgsAmount(): int
    {
        return $this->availableRgsAmount;
    }

    /**
     * @param int $availableRgsAmount
     *
     * @return void
     */
    public function setAvailableRgsAmount(int $availableRgsAmount): void
    {
        $this->availableRgsAmount = $availableRgsAmount;
    }

    /**
     * @return int
     */
    public function getAppliedPackagesAmount(): int
    {
        return $this->appliedPackagesAmount;
    }

    /**
     * @param int $appliedPackagesAmount
     *
     * @return void
     */
    public function setAppliedPackagesAmount(int $appliedPackagesAmount): void
    {
        $this->appliedPackagesAmount = $appliedPackagesAmount;
    }

    /**
     * @return int
     */
    public function getAppliedRGsAmount(): int
    {
        return $this->appliedRGsAmount;
    }

    /**
     * @param int $appliedRGsAmount
     *
     * @return void
     */
    public function setAppliedRGsAmount(int $appliedRGsAmount): void
    {
        $this->appliedRGsAmount = $appliedRGsAmount;
    }

    /**
     * @return int
     */
    public function getAppliedSecurityFixesAmount(): int
    {
        return $this->appliedSecurityFixesAmount;
    }

    /**
     * @param int $appliedSecurityFixesAmount
     *
     * @return void
     */
    public function setAppliedSecurityFixesAmount(int $appliedSecurityFixesAmount): void
    {
        $this->appliedSecurityFixesAmount = $appliedSecurityFixesAmount;
    }
}
