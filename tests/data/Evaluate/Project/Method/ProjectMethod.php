<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\Method;

use TestCore\Method\CoreMethod;
use TestCore\Method\CoreMethodInterface;

class ProjectMethod extends CoreMethod implements CoreMethodInterface
{
    /**
     * @return void
     */
    public function __invoke(): void
    {
    }

    /**
     * @return string
     */
    public function superCoreMethodSuccess(): string
    {
        return 'Core Method';
    }

    /**
     * @return string
     */
    public function projectMethodNameError(): string
    {
        return 'Custom methodName. NotExtended. Without Test project prefix.';
    }

    /**
     * @return string
     */
    public function getTestProjectMethodNameSuccess(): string
    {
        return 'Custom methodName. NotExtended. With Test project prefix.';
    }

    /**
     * @return string
     */
    public function superMethodFromCoreInterfaceSuccess(): string
    {
        return 'SuperMethod from core interface';
    }
}
