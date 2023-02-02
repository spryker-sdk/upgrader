<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Report\Dto;

class ReportPayloadDto
{
    /**
     * @var array<\Upgrade\Domain\Entity\Package>
     */
    protected array $requiredPackages;

    /**
     * @var array<\Upgrade\Domain\Entity\Package>
     */
    protected array $devRequiredPackages;

    /**
     * @param array<\Upgrade\Domain\Entity\Package> $requiredPackages
     * @param array<\Upgrade\Domain\Entity\Package> $devRequiredPackages
     */
    public function __construct(array $requiredPackages = [], array $devRequiredPackages = [])
    {
        $this->requiredPackages = $requiredPackages;
        $this->devRequiredPackages = $devRequiredPackages;
    }

    /**
     * @return array<\Upgrade\Domain\Entity\Package>
     */
    public function getRequiredPackages(): array
    {
        return $this->requiredPackages;
    }

    /**
     * @return array<\Upgrade\Domain\Entity\Package>
     */
    public function getDevRequiredPackages(): array
    {
        return $this->devRequiredPackages;
    }
}
