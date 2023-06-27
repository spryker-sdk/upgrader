<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\PackageManager\Reader;

class ComposerJsonReader extends AbstractComposerReader
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
        $path = static::COMPOSER_JSON;

        if ($this->directory !== '') {
            $path = $this->directory . DIRECTORY_SEPARATOR . $path;
        }

        return $this->readFromPath($path);
    }
}
