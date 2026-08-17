<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Dto;

class ReleaseGroupStatDto
{
    protected int $availableRgsAmount = 0;

    protected int $appliedPackagesAmount = 0;

    protected int $appliedRGsAmount = 0;

    protected int $appliedSecurityFixesAmount = 0;

    public function getAvailableRgsAmount(): int
    {
        return $this->availableRgsAmount;
    }

    public function setAvailableRgsAmount(int $availableRgsAmount): void
    {
        $this->availableRgsAmount = $availableRgsAmount;
    }

    public function getAppliedPackagesAmount(): int
    {
        return $this->appliedPackagesAmount;
    }

    public function setAppliedPackagesAmount(int $appliedPackagesAmount): void
    {
        $this->appliedPackagesAmount = $appliedPackagesAmount;
    }

    public function getAppliedRGsAmount(): int
    {
        return $this->appliedRGsAmount;
    }

    public function setAppliedRGsAmount(int $appliedRGsAmount): void
    {
        $this->appliedRGsAmount = $appliedRGsAmount;
    }

    public function getAppliedSecurityFixesAmount(): int
    {
        return $this->appliedSecurityFixesAmount;
    }

    public function setAppliedSecurityFixesAmount(int $appliedSecurityFixesAmount): void
    {
        $this->appliedSecurityFixesAmount = $appliedSecurityFixesAmount;
    }
}
