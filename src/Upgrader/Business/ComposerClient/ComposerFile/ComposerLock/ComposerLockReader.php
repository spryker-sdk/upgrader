<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\ComposerClient\ComposerFile\ComposerLock;

use Upgrader\Business\ComposerClient\ComposerFile\AbstractJsonReader;

class ComposerLockReader extends AbstractJsonReader
{
    public const FILENAME_LOCK = 'composer.lock';

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return self::FILENAME_LOCK;
    }
}
