<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Core\Infrastructure\Services;

use Core\Infrastructure\Exception\FileNotFoundException;

class ComposerLockReader
{
    /**
     * @var string
     */
    protected const COMPOSER_LOCK = 'composer.lock';

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function readComposerLockDataByKey(string $key)
    {
        $fileData = $this->readFromPath(static::COMPOSER_LOCK);

        return $fileData[$key] ?? null;
    }

    /**
     * @return array
     */
    public function readComposerLock(): array
    {
        return $this->readFromPath(static::COMPOSER_LOCK);
    }

    /**
     * @param string $path
     *
     * @throws \Upgrade\Infrastructure\Exception\FileNotFoundException
     *
     * @return array
     */
    protected function readFromPath(string $path): array
    {
        if (!file_exists($path)) {
            throw new FileNotFoundException(sprintf('%s file is not exists.', $path));
        }

        return json_decode((string)file_get_contents($path), true);
    }
}
