<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Infrastructure\Adapter;

use CodeCompliance\Domain\Adapter\CodeBaseAdapterInterface;
use ReflectionClass;

class CodeBaseAdapter implements CodeBaseAdapterInterface
{
    /**
     * @param string $classNamespace
     *
     * @return \ReflectionClass|null
     */
    public function getClassReflectionByClassNamespace(string $classNamespace): ?ReflectionClass
    {
        if (class_exists($classNamespace) || interface_exists($classNamespace)) {
            return new ReflectionClass($classNamespace);
        }

        return null;
    }
}
