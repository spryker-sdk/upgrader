<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\Filters;

use Codebase\Application\Dto\CodebaseInterface;

class IgnoreListFilter implements FilterInterface
{
    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return Filters::IGNORE_LIST_FILTER;
    }

    /**
     * @param array $sources
     *
     * @return array
     */
    public function filter(array $sources): array
    {
        return array_filter($sources, function (CodebaseInterface $source) {
            $coreParent = $source->getCoreParent();

            return ($coreParent && !$this->isClassFromIgnoreList((string)$coreParent->getClassName()));
        });
    }

    /**
     * @param string $classNamespace
     *
     * @return bool
     */
    protected function isClassFromIgnoreList(string $classNamespace): bool
    {
        $ignorePatternList = $this->getIgnoreSuffixes();

        foreach ($ignorePatternList as $pattern) {
            if (preg_match($pattern, $this->reverseSlash($classNamespace))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $source
     *
     * @return string
     */
    protected function reverseSlash(string $source): string
    {
        return str_replace('\\', '/', $source);
    }

    /**
     * @return array<string>
     */
    public function getIgnoreSuffixes(): array
    {
        return [
            '/\/Kernel\//',
            '/\/\w+Bootstrap$/',
            '/\/\w+ConfigurationProvider$/',
            '/\/Development\//',
            '/^Spryker\/Zed\/\w+DataImport\//',
            '/^Spryker\/Shared\/Twig\/Twig\/TwigFunctionProvider$/',
        ];
    }
}
