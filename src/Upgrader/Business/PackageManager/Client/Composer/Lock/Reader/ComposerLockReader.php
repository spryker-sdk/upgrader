<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\PackageManager\Client\Composer\Lock\Reader;

use Upgrader\Business\Exception\UpgraderException;

class ComposerLockReader implements ComposerLockReaderInterface
{
    protected const COMPOSER_LOCK = 'composer.lock';

    /**
     * @return array
     */
    public function read(): array
    {
        return $this->readFromPath(static::COMPOSER_LOCK);
    }

    /**
     * @param string $path
     *
     * @throws \Upgrader\Business\Exception\UpgraderException
     *
     * @return array
     */
    protected function readFromPath(string $path): array
    {
        if (!file_exists($path)) {
            throw new UpgraderException('File is not exist: ' . $path);
        }

        return json_decode((string)file_get_contents($path), true);
    }
}
