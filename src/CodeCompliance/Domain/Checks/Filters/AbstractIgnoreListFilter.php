<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace CodeCompliance\Domain\Checks\Filters;

abstract class AbstractIgnoreListFilter implements FilterInterface
{
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
    protected function getIgnoreSuffixes(): array
    {
        return [
            '/\/Kernel\//',
            '/^Twig\/Environment$/',
            '/\/\w+Bootstrap$/',
            '/\/\w+ConfigurationProvider$/',
            '/\/Development\//',
            '/^Spryker\/Zed\/\w+DataImport\//',
            '/^Spryker\/Shared\/Twig\/TwigFunctionProvider$/',
            '/^Spryker\/Zed\/Gui\/Communication\/Table\/AbstractTable$/',
            '/^Spryker\/Zed\/Gui\/Communication\/Tabs\/AbstractTabs$/',
            '/^Spryker\/DecimalObject\//',
        ];
    }
}
