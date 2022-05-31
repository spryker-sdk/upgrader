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
     * @var string
     */
    public const PLUGIN_FILTER = 'PLUGIN_FILTER';

    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return static::PLUGIN_FILTER;
    }

    /**
     * @param array<\Codebase\Application\Dto\CodebaseInterface> $sources
     *
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    public function filter(array $sources): array
    {
        return array_filter($sources, function (CodebaseInterface $source) {
            return !$this->isPlugin($source->getReflection());
        });
    }

    /**
     * @phpstan-template T of \Codebase\Application\Dto\CodebaseInterface
     *
     * @param \ReflectionClass<T> $class
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
