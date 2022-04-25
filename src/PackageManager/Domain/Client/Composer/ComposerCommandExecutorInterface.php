<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PackageManager\Domain\Client\Composer;

use PackageManager\Domain\Dto\Collection\PackageDtoCollection;
use PackageManager\Domain\Dto\PackageManagerResponseDto;

interface ComposerCommandExecutorInterface
{
    /**
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function require(PackageDtoCollection $packageCollection): PackageManagerResponseDto;

    /**
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function requireDev(PackageDtoCollection $packageCollection): PackageManagerResponseDto;

    /**
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function update(): PackageManagerResponseDto;
}
