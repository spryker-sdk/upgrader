<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\PackageManager\Client\Composer\Json\Reader;

use Upgrader\Business\Exception\UpgraderException;

class ComposerJsonReader implements ComposerJsonReaderInterface
{
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
     * @throws \Upgrader\Business\Exception\UpgraderException
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
