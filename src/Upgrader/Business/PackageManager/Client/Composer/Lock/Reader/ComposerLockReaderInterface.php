<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\PackageManager\Client\Composer\Lock\Reader;

interface ComposerLockReaderInterface
{
    /**
     * @return array
     */
    public function read(): array;
}
