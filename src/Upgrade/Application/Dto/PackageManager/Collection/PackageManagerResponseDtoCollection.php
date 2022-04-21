<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Dto\PackageManager\Collection;

use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto;

class PackageManagerResponseDtoCollection extends ResponseCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return PackageManagerResponseDto::class;
    }
}
