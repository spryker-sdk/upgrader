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
     * @var array<string>
     */
    protected array $integratorWarnings;

    /**
     * @param array<\Upgrade\Domain\Entity\Package> $requiredPackages
     * @param array<\Upgrade\Domain\Entity\Package> $devRequiredPackages
     * @param array<string> $integratorWarnings
     */
    public function __construct(
        array $requiredPackages = [],
        array $devRequiredPackages = [],
        array $integratorWarnings = []
    ) {
        $this->requiredPackages = $requiredPackages;
        $this->devRequiredPackages = $devRequiredPackages;
        $this->integratorWarnings = $integratorWarnings;
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

    /**
     * @return array<string>
     */
    public function getIntegratorWarnings(): array
    {
        return $this->integratorWarnings;
    }
}
