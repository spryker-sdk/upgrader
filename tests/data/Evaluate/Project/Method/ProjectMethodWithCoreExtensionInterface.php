<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\Method;

use TestCore\Method\CoreMethodInterface;

interface ProjectMethodWithCoreExtensionInterface extends CoreMethodInterface
{
    /**
     * @return string
     */
    public function projectMethodNameError(): string;

    /**
     * @return string
     */
    public function getTestProjectMethodNameSuccess(): string;
}
