<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Composer\ComposerLock;

use Upgrader\Business\Composer\Helper\JsonFile\JsonFileReadHelper;

class ComposerLockReader implements ComposerLockReaderInterface
{
    protected const FILENAME_LOCK = 'composer.lock';

    /**
     * @var \Upgrader\Business\Composer\Helper\JsonFile\JsonFileReadHelper
     */
    protected $jsonFileReadHelper;

    /**
     * @param \Upgrader\Business\Composer\Helper\JsonFile\JsonFileReadHelper $jsonFileReadHelper
     */
    public function __construct(JsonFileReadHelper $jsonFileReadHelper)
    {
        $this->jsonFileReadHelper = $jsonFileReadHelper;
    }

    /**
     * @return array
     */
    public function read(): array
    {
        return $this->jsonFileReadHelper->readFromPath(self::FILENAME_LOCK);
    }
}
