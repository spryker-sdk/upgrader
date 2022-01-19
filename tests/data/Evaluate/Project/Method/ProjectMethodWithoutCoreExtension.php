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
    public function projectMethodName(): string
    {
        return 'MethodName without Test project prefix.';
    }

    /**
     * @return string
     */
    public function testProjectMethodName(): string
    {
        return 'MethodName with Test project prefix.';
    }
}
