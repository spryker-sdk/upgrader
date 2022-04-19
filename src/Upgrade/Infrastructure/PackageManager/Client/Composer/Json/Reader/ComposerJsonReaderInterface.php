<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManager\Client\Composer\Json\Reader;

interface ComposerJsonReaderInterface
{
    /**
     * @return array
     */
    public function read(): array;
}
