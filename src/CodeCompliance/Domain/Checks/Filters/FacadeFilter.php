<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\Filters;

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
     * @param array $sources
     *
     * @return array
     */
    public function filter(array $sources): array
    {
        return array_filter($sources, function ($source) {
            $className = $source->getClassName();

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
