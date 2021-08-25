<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Composer\ComposerLock;

use Upgrader\Business\Composer\Helper\JsonFile\JsonFileReadHelper;

class ComposerLockReader implements ComposerLockReaderInterface
{
    /**
     * @var string
     */
    private $composerLockFilePath;

    /**
     * @param string $composerLockFilePath
     */
    public function __construct(string $composerLockFilePath)
    {
        $this->composerLockFilePath = $composerLockFilePath;
    }

    /**
     * @return array
     */
    public function read(): array
    {
        return JsonFileReadHelper::readFromPath($this->composerLockFilePath);
    }
}
