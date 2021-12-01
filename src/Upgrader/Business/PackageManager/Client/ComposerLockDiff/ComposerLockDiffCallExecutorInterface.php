<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Client\ComposerLockDiff;

use Upgrader\Business\PackageManager\Response\PackageManagerResponse;

interface ComposerLockDiffCallExecutorInterface
{
    /**
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    public function getComposerLockDiff(): PackageManagerResponse;
}
