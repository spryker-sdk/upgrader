<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\Composer\Reader;

use Upgrade\Infrastructure\Exception\FileNotFoundException;

class ComposerReader
{
    /**
     * @var string
     */
    protected const COMPOSER_JSON = 'composer.json';

    /**
     * @var string
     */
    protected const COMPOSER_LOCK = 'composer.lock';

    /**
     * @return array
     */
    public function readComposerLock(): array
    {
        return $this->readFromPath(static::COMPOSER_LOCK);
    }

    /**
     * @return array
     */
    public function readComposerJson(): array
    {
        return $this->readFromPath(static::COMPOSER_JSON);
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
