<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;

class DummyPackageFilterItem implements ReleaseGroupFilterItemInterface
{
    /**
     * @var string
     */
    protected const DUMMY_MODULES_REGEXP = '/^spryker.*\/(dummy-.*|.*-example)$/';

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroupDto
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto
     */
    public function filter(ReleaseGroupDto $releaseGroupDto): ReleaseGroupDto
    {
        $filteredModuleCollection = new ModuleDtoCollection();

        foreach ($releaseGroupDto->getModuleCollection()->toArray() as $module) {
            if (preg_match(static::DUMMY_MODULES_REGEXP, trim($module->getName()))) {
                continue;
            }

            $filteredModuleCollection->add($module);
        }

        $releaseGroupDto->setModuleCollection($filteredModuleCollection);

        return $releaseGroupDto;
    }
}
