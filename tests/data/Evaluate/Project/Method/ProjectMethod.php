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
    public function superCoreMethod(): string
    {
        return 'Core Method';
    }

    /**
     * @return string
     */
    public function projectMethodName(): string
    {
        return 'Custom methodName. NotExtended. Without Test project prefix.';
    }

    /**
     * @return string
     */
    public function testProjectMethodName(): string
    {
        return 'Custom methodName. NotExtended. With Test project prefix.';
    }

    /**
     * @return string
     */
    public function superMethodFromCore(): string
    {
        return 'SuperMethod from core interface';
    }
}
