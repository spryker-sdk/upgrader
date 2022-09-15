<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\Filters;

use Codebase\Application\Dto\CodebaseInterface;

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
        return array_filter($sources, function ($source) {
            return $this->isBusinessFactory($source);
        });
    }

    /**
     * @param \Codebase\Application\Dto\CodebaseInterface $source
     *
     * @return bool
     */
    protected function isBusinessFactory(CodebaseInterface $source): bool
    {
        $className = $source->getClassName();
        if (!$className) {
            return false;
        }

        return (bool)preg_match(static::PATTERN, $className);
    }
}
