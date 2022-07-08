<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Domain\Resolves\Filters;

class PrivateApiFilter implements FilterInterface
{
    /**
     * @var string
     */
    public const PRIVATE_API_FILTER = 'PRIVATE_API_FILTER';

    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return static::PRIVATE_API_FILTER;
    }

    /**
     * @param array<\Codebase\Application\Dto\CodebaseInterface> $sources
     *
     * @return array<\Codebase\Application\Dto\CodebaseInterface>
     */
    public function filter(array $sources): array
    {
        return array_filter($sources, function ($source) {
            $className = $source->getClassName();

            return ($className && !$this->isPublicApi($className));
        });
    }

    /**
     * @param string $classNamespace
     *
     * @return bool
     */
    protected function isPublicApi(string $classNamespace): bool
    {
        $patternList = $this->getPublicApiSuffixes();

        foreach ($patternList as $pattern) {
            if (preg_match($pattern, $classNamespace)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string>
     */
    protected function getPublicApiSuffixes(): array
    {
        return [
            '/\w+Config$/',
            '/\w+Controller$/',
            '/\w+Service$/',
            '/\w+Client$/',
            '/\w+Facade$/',
            '/\w+QueryContainer$/',
            '/\w+PluginInterface$/',
            '/\w+ServiceInterface$/',
            '/\w+ClientInterface$/',
            '/\w+FacadeInterface$/',
            '/\w+QueryContainerInterface$/',
        ];
    }
}
