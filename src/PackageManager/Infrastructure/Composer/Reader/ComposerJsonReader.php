<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PackageManager\Infrastructure\Composer\Reader;

use PackageManager\Domain\Client\Composer\Reader\ComposerJsonReaderInterface;
use Upgrade\Infrastructure\Exception\UpgraderException;

class ComposerJsonReader implements ComposerJsonReaderInterface
{
    /**
     * @var string
     */
    protected const COMPOSER_JSON = 'composer.json';

    /**
     * @return array
     */
    public function read(): array
    {
        return $this->readFromPath(static::COMPOSER_JSON);
    }

    /**
     * @param string $path
     *
     * @throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     * @return array
     */
    protected function readFromPath(string $path): array
    {
        if (!file_exists($path)) {
            throw new UpgraderException(sprintf('%s file is not exists.', $path));
        }

        return json_decode((string)file_get_contents($path), true);
    }
}
