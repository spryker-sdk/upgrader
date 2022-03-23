<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\Method;

class ProjectMethodWithCoreExtension implements ProjectMethodWithCoreExtensionInterface
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
    public function superMethodFromCoreInterfaceSuccess(): string
    {
        return 'SuperMethod from core interface';
    }

    /**
     * @return string
     */
    public function projectMethodNameError(): string
    {
        return 'MethodName with core interface extending and without Test project prefix.';
    }

    /**
     * @return string
     */
    public function getTestProjectMethodNameSuccess(): string
    {
        return 'MethodName with core interface and with Test project prefix.';
    }
}
