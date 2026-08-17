<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrader;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class UpgraderBundle extends Bundle
{
    protected function createContainerExtension(): UpgraderExtension
    {
        return new UpgraderExtension();
    }
}
