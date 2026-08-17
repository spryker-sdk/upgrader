<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\Module\BetaMajorModule;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;

interface BetaMajorModulesFetcherInterface
{
    /**
     * @return array<\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto>
     */
    public function getBetaMajorsNotInstalledInDev(ModuleDtoCollection $moduleDtoCollection): array;
}
