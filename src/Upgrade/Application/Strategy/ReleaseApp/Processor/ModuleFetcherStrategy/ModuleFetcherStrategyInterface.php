<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcherStrategy;

use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Domain\Entity\Collection\PackageCollection;

interface ModuleFetcherStrategyInterface
{
    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManagerResponseDto
     */
    public function fetchModules(PackageCollection $packageCollection): PackageManagerResponseDto;

    /**
     * @return bool
     */
    public function isApplicable(): bool;
}
