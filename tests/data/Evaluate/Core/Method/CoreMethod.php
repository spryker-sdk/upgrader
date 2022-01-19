<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestCore\Method;

class CoreMethod
{
    /**
     * @return string
     */
    public function superCoreMethod(): string
    {
        return 'Core Method';
    }

    /**
     * @return string
     */
    public function testSuperCoreMethod(): string
    {
        return 'Core Method';
    }
}
