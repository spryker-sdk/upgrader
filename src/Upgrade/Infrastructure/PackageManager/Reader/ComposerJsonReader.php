<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManager\Reader;

use Upgrade\Infrastructure\Exception\FileNotFoundException;

class ComposerJsonReader implements ComposerJsonReaderInterface
{
    /**
     * @var string
     */
    protected const COMPOSER_JSON = 'composer.json';

    /**
     * @return array<mixed>
     */
    public function read(): array
    {
        return $this->readFromPath(static::COMPOSER_JSON);
    }

    /**
     * @param string $path
     *
     * @throws \Upgrade\Infrastructure\Exception\FileNotFoundException
     *
     * @return array<mixed>
     */
    protected function readFromPath(string $path): array
    {
        if (!file_exists($path)) {
            throw new FileNotFoundException(sprintf('%s file is not exists.', $path));
        }

        return json_decode((string)file_get_contents($path), true);
    }
}
