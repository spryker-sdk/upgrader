<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManager\Reader;

use Upgrade\Application\Exception\UpgraderException;

class ComposerLockReader implements ComposerLockReaderInterface
{
    /**
     * @var string
     */
    protected const COMPOSER_LOCK = 'composer.lock';

    /**
     * @return array<mixed>
     */
    public function read(): array
    {
        return $this->readFromPath(static::COMPOSER_LOCK);
    }

    /**
     * @param string $path
     *
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return array<mixed>
     */
    protected function readFromPath(string $path): array
    {
        if (!file_exists($path)) {
            throw new UpgraderException('File is not exist: ' . $path);
        }

        return json_decode((string)file_get_contents($path), true);
    }
}
