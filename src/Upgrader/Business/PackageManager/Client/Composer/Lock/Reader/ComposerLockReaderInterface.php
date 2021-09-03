<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Client\Composer\Lock\Reader;

interface ComposerLockReaderInterface
{
    /**
     * @return array
     */
    public function read(): array;
}
