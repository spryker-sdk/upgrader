<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\Filters;

class BusinessModelFilter implements FilterInterface
{
    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return Filters::BUSINESS_MODEL_FILTER;
    }

    /**
     * @param array $sources
     *
     * @return array
     */
    public function filter(array $sources): array
    {
        return array_filter($sources, function ($source) {
            return $this->isBusinessModel((string)$source->getClassName());
        });
    }

    /**
     * @param string $classNamespace
     *
     * @return bool
     */
    protected function isBusinessModel(string $classNamespace): bool
    {
        $nonBusinessSuffixes = $this->getNonBusinessModelSuffixes();
        foreach ($nonBusinessSuffixes as $pattern) {
            if (preg_match($pattern, $classNamespace)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<string>
     */
    public function getNonBusinessModelSuffixes(): array
    {
        return [
            '/\w+Factory$/',
            '/\w+DependencyProvider$/',
            '/\w+Config$/',
            '/\w+Controller$/',
            '/\w+Service$/',
            '/\w+Client$/',
            '/\w+Facade$/',
            '/\w+EntityManager$/',
            '/\w+Repository$/',
            '/\w+QueryContainer$/',
            '/\w+PluginInterface$/',
            '/\w+ServiceInterface$/',
            '/\w+ClientInterface$/',
            '/\w+FacadeInterface$/',
            '/\w+EntityManagerInterface$/',
            '/\w+RepositoryInterface$/',
            '/\w+QueryContainerInterface$/',
            '/\w+Widget$/',
            '/\w+WidgetInterface$/',
            '/\w+Plugin$/',
            '/\w+PluginInterface$/',
            '/\w+EventSubscriber$/',
            '/\w+EventSubscriberInterface$/',
        ];
    }
}
