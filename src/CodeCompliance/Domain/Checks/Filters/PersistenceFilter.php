<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\Filters;

use Codebase\Application\Dto\CodebaseInterface;

class PersistenceFilter implements FilterInterface
{
    /**
     * @var string
     */
    public const PERSISTENCE_FILTER = 'PERSISTENCE_FILTER';

    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return static::PERSISTENCE_FILTER;
    }

    /**
     * @param array<\Codebase\Application\Dto\CodebaseInterface> $sources
     *
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    public function filter(array $sources): array
    {
        return array_filter($sources, function (CodebaseInterface $source) {
            $className = $source->getClassName();

            return ($className && $this->isRepositoryOrEntityManager($className));
        });
    }

    /**
     * @param string $classNamespace
     *
     * @return bool
     */
    protected function isRepositoryOrEntityManager(string $classNamespace): bool
    {
        $persistencePatternList = $this->getPersistenceSuffixes();

        foreach ($persistencePatternList as $pattern) {
            if (preg_match($pattern, $classNamespace)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string>
     */
    protected function getPersistenceSuffixes(): array
    {
        return [
            '/\w+EntityManager$/',
            '/\w+Repository$/',
        ];
    }
}
