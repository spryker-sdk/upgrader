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
     * @return string
     */
    public function getFilterName(): string
    {
        return Filters::PERSISTENCE_FILTER;
    }

    /**
     * @param array $sources
     *
     * @return array
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