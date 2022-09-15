<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\PackageManager\Reader;

interface ComposerJsonReaderInterface
{
    /**
     * @return array<mixed>
     */
    public function read(): array;

    /**
     * @param string $directory
     *
     * @return void
     */
    public function setDirectory(string $directory): void;
}
