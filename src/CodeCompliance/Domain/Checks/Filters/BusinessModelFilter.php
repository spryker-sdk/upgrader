<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\Filters;

use Codebase\Application\Dto\ClassCodebaseDto;

class BusinessModelFilter implements FilterInterface
{
    /**
     * @var string
     */
    public const BUSINESS_MODEL_FILTER = 'BUSINESS_MODEL_FILTER';

    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return static::BUSINESS_MODEL_FILTER;
    }

    /**
     * @param array<\Codebase\Application\Dto\ClassCodebaseDto> $sources
     *
     * @return array<\Codebase\Application\Dto\ClassCodebaseDto>
     */
    public function filter(array $sources): array
    {
        return array_filter($sources, function (ClassCodebaseDto $source) {
            return $this->isBusinessModel((string)$source->getName());
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
            '/\w+DependencyInjector$/',
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
