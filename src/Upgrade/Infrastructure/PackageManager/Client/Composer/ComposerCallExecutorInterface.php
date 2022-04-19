<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManager\Client\Composer;

use Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection;
use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto;

interface ComposerCallExecutorInterface
{
    /**
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto
     */
    public function require(PackageDtoCollection $packageCollection): PackageManagerResponseDtoDto;

    /**
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto
     */
    public function requireDev(PackageDtoCollection $packageCollection): PackageManagerResponseDtoDto;

    /**
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto
     */
    public function update(): PackageManagerResponseDtoDto;
}
