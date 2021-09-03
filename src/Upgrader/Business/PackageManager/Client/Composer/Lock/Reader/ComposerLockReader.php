<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
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
