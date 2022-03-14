<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\Filters;

use Codebase\Application\Dto\CodebaseInterface;
use ReflectionClass;

class PluginFilter implements FilterInterface
{
    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return Filters::PLUGIN_FILTER;
    }

    /**
     * @param array $sources
     *
     * @return array
     */
    public function filter(array $sources): array
    {
        return array_filter($sources, function (CodebaseInterface $source) {
            return !$this->isPlugin($source->getReflection());
        });
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return bool
     */
    protected function isPlugin(ReflectionClass $class): bool
    {
        $pattern = '/.*Plugin$/';
        $parent = $class->getParentClass();
        $className = $class->getShortName();
        if (!$parent) {
            return false;
        }

        return (preg_match($pattern, $className)) && (preg_match($pattern, $parent->getShortName()));
    }
}
