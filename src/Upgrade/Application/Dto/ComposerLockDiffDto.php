<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Dto;

class ComposerLockDiffDto
{
    /**
     * @var array<\Upgrade\Domain\Entity\Package>
     */
    protected array $requiredPackages;

    /**
     * @var array<\Upgrade\Domain\Entity\Package>
     */
    protected array $requiredDevPackages;

    /**
     * @param array<\Upgrade\Domain\Entity\Package> $requiredPackages
     * @param array<\Upgrade\Domain\Entity\Package> $requiredDevPackages
     */
    public function __construct(array $requiredPackages = [], array $requiredDevPackages = [])
    {
        $this->requiredPackages = $requiredPackages;
        $this->requiredDevPackages = $requiredDevPackages;
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
    public function getRequiredDevPackages(): array
    {
        return $this->requiredDevPackages;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return (count($this->requiredPackages) + count($this->requiredDevPackages)) === 0;
    }
}
