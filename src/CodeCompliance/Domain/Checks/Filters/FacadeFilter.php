<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\Filters;

use Codebase\Application\Dto\ClassCodebaseDto;

class FacadeFilter implements FilterInterface
{
    /**
     * @var string
     */
    public const FACADE_FILTER = 'FACADE_FILTER';

    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return static::FACADE_FILTER;
    }

    /**
     * @param array<\Codebase\Application\Dto\ClassCodebaseDto> $sources
     *
     * @return array<\Codebase\Application\Dto\ClassCodebaseDto>
     */
    public function filter(array $sources): array
    {
        return array_filter($sources, function (ClassCodebaseDto $source) {
            $className = $source->getName();

            return ($className && $this->isFacade($className));
        });
    }

    /**
     * @param string $classNamespace
     *
     * @return bool
     */
    protected function isFacade(string $classNamespace): bool
    {
        return (bool)preg_match('/\w+Facade$/', $classNamespace);
    }
}
