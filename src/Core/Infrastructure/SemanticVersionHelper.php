<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Core\Infrastructure;

class SemanticVersionHelper
{
    /**
     * @param string $semanticVersion
     *
     * @return int|null
     */
    public static function getMajorVersion(string $semanticVersion): ?int
    {
        if (!static::isSemanticVersion($semanticVersion)) {
            return null;
        }

        $versionParts = explode('.', $semanticVersion);
        if (!$versionParts) {
            return null;
        }

        return (int)array_shift($versionParts);
    }

    /**
     * @param string $version
     *
     * @return bool
     */
    protected static function isSemanticVersion(string $version): bool
    {
        return preg_match('/^\d+\.\d+\.\d+$/', $version) === 1;
    }
}
