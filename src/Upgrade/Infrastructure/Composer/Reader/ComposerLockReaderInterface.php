<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Composer\Reader;

interface ComposerLockReaderInterface
{
    /**
     * @return array
     */
    public function read(): array;
}
