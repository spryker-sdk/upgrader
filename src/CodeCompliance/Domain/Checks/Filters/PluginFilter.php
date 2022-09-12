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
     * @var string
     */
    protected const PLUGIN_CLASS_SUFFIX = '/.*Plugin$/';

    /**
     * @var string
     */
    protected const PLUGIN_DIR_NAME = '/\/Plugin\//';

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
        return preg_match(static::PLUGIN_DIR_NAME, $this->reverseSlash($class->getName())) ||
            preg_match(static::PLUGIN_CLASS_SUFFIX, $class->getShortName());
    }

    /**
     * @param string $source
     *
     * @return string
     */
    protected function reverseSlash(string $source): string
    {
        return str_replace('\\', '/', $source);
    }
}
