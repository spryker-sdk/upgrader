<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\Filters;

use Codebase\Application\Dto\ClassCodebaseDto;

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
     * @param array<\Codebase\Application\Dto\ClassCodebaseDto> $sources
     *
     * @return array<\Codebase\Application\Dto\ClassCodebaseDto>
     */
    public function filter(array $sources): array
    {
        return array_filter($sources, function (ClassCodebaseDto $source) {
            return !$this->isPlugin($source);
        });
    }

    /**
     * @param \Codebase\Application\Dto\ClassCodebaseDto $classCodebaseDto
     *
     * @return bool
     */
    protected function isPlugin(ClassCodebaseDto $classCodebaseDto): bool
    {
        $pattern = '/.*Plugin$/';

        $parent = $classCodebaseDto->getParentClass();
        $className = $classCodebaseDto->getShortName();
        if (!$parent) {
            return false;
        }

        return (preg_match($pattern, $className)) && (preg_match($pattern, $parent->getShortName()));
    }
}
