<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\PackageManager\Reader;

class ComposerLockReader extends AbstractComposerReader
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
        $path = static::COMPOSER_LOCK;

        if ($this->directory !== '') {
            $path = $this->directory . DIRECTORY_SEPARATOR . $path;
        }

        return $this->readFromPath($path);
    }
}
