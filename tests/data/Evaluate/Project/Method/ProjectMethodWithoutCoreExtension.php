<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\Method;

class ProjectMethodWithoutCoreExtension implements ProjectMethodWithoutCoreExtensionInterface
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
    public function projectMethodNameSuccess(): string
    {
        return 'MethodName without extending and Test project prefix.';
    }

    /**
     * @return string
     */
    public function testProjectMethodNameSuccess(): string
    {
        return 'MethodName without extending and with Test project prefix.';
    }
}
