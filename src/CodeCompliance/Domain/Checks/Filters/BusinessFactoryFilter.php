<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\Filters;

use Codebase\Application\Dto\ClassCodebaseDto;

class BusinessFactoryFilter implements FilterInterface
{
    /**
     * @var string
     */
    public const BUSINESS_FACTORY_FILTER = 'BUSINESS_FACTORY_FILTER';

    /**
     * @var string
     */
    public const PATTERN = '/.*(BusinessFactory)$/';

    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return static::BUSINESS_FACTORY_FILTER;
    }

    /**
     * @param array<\Codebase\Application\Dto\CodebaseInterface> $sources
     *
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    public function filter(array $sources): array
    {
        return array_filter($sources, function (ClassCodebaseDto $source) {
            return $this->isBusinessFactory($source);
        });
    }

    /**
     * @param \Codebase\Application\Dto\ClassCodebaseDto $source
     *
     * @return bool
     */
    protected function isBusinessFactory(ClassCodebaseDto $source): bool
    {
        $className = $source->getName();
        if (!$className) {
            return false;
        }

        return (bool)preg_match(static::PATTERN, $className);
    }
}
