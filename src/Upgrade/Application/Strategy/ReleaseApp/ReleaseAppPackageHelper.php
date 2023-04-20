<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp;

class ReleaseAppPackageHelper
{
    /**
     * @param string $releaseAppPackageName
     *
     * @return string
     *
     * spryker-shop/shop.shop-ui -> spryker-shop/shop-ui
     */
    public static function normalizePackageName(string $releaseAppPackageName): string
    {
        return (string)preg_replace('/\/[a-z]*\./m', '/', $releaseAppPackageName);
    }
}
