<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\Method;

use TestCore\Method\AbstractCorePlugin;

class ProjectPlugin extends AbstractCorePlugin
{
    /**
     * @return string
     */
    public function superCoreAbstractMethodSuccess(): string
    {
        return 'we should skip Plugin in general';
    }

    /**
     * @return string
     */
    public function projectMethod(): string
    {
        return 'we should skip Plugin in general';
    }
}
