<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestCore\Method;

abstract class AbstractCorePlugin
{
    /**
     * @return string
     */
    abstract public function superCoreAbstractMethodSuccess(): string;

    /**
     * @return string
     */
    public function superCoreMethodSuccess(): string
    {
        return 'we should skip Plugin in general';
    }
}
