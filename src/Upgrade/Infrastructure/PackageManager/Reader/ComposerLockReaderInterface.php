<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManager\Reader;

interface ComposerLockReaderInterface
{
    /**
     * @return array<mixed>
     */
    public function read(): array;
}
