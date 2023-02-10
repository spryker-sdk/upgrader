<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\EnvParser;

use InvalidArgumentException;

class EnvFetcher
{
    /**
     * @var array<string>
     */
    protected const FALSE_VALUES = ['0', 'false'];

    /**
     * @var array<string>
     */
    protected const TRUE_VALUES = ['1', 'true'];

    /**
     * @param string $varName
     * @param bool|null $defaultValue
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public static function getBool(string $varName, ?bool $defaultValue = null): bool
    {
        $value = getenv($varName);

        if ($value === false && $defaultValue === null) {
            throw new InvalidArgumentException(sprintf('Required environment variable `%s` is not found', $varName));
        }

        if ($value === false && $defaultValue !== null) {
            return $defaultValue;
        }

        $value = trim((string)$value);

        if (in_array($value, static::FALSE_VALUES, true)) {
            return false;
        }

        if (in_array($value, static::TRUE_VALUES, true)) {
            return true;
        }

        if ($defaultValue !== null) {
            return $defaultValue;
        }

        throw new InvalidArgumentException(sprintf('Unsupported `%s` values for `%s`', $value, $varName));
    }
}
