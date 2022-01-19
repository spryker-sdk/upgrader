<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\Used\DependencyProvider;

use TestCore\Used\DependencyProvider\ProductPageSearchDependencyProvider as CoreProductPageSearchDependencyProvider;

class ProductPageSearchDependencyProvider extends CoreProductPageSearchDependencyProvider
{
    /**
     * @var string
     */
    public const TEST_WITH_PREFIX_CONSTANT_NAME = 'constant name has project specific prefix';
}
