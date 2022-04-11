<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestCore\Method;

use PHPUnit\Framework\Constraint\Count;

class MethodFromThirdPartyLibrary extends Count
{
    /**
     * @return string
     */
    public function toString(): string
    {
        return parent::toString();
    }
}
