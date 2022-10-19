<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Codebase\Infrastructure\ToolingConfigurationReader\Reader;

abstract class AbstractReader implements ReaderInterface
{
    /**
     * @param array<string> $array
     *
     * @return bool
     */
    protected function isSequentialArrayOfString(array $array): bool
    {
        if (count(array_filter(array_keys($array), 'is_string'))) {
            return false;
        }

        if (count(array_filter(array_values($array), 'is_string')) !== count($array)) {
            return false;
        }

        return true;
    }
}
