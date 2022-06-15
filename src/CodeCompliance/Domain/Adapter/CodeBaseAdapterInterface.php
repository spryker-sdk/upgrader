<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Adapter;

use ReflectionClass;

interface CodeBaseAdapterInterface
{
    /**
     * @param string $classNamespace
     *
     * @return \ReflectionClass|null
     */
    public function getClassReflectionByClassNamespace(string $classNamespace): ?ReflectionClass;
}
