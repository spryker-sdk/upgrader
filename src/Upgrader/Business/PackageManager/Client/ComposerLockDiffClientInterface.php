<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Client;

use Upgrader\Business\PackageManager\Response\Collection\PackageManagerResponseCollection;

interface ComposerLockDiffClientInterface
{
    /**
     * @return \Upgrader\Business\PackageManager\Response\Collection\PackageManagerResponseCollection
     */
    public function getComposerLockDiff(): PackageManagerResponseCollection;
}
