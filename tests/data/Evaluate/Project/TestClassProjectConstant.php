<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject;

use TestCore\TestClassCoreConstant;

class TestClassProjectConstant extends TestClassCoreConstant
{
    /**
     * @var string
     */
    protected const TEST_WITH_PREFIX_CONSTANT_NAME = 'constant name has project specific prefix';

    /**
     * @var string
     */
    protected const WITHOUT_PREFIX_CONSTANT_NAME = 'constant name without project specific prefix.';

    /**
     * @var string
     */
    protected const WITHOUT_PREFIX_CONSTANT_NAME_FROM_CORE = 'constant from the core';
}
