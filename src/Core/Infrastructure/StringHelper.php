<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Core\Infrastructure;

class StringHelper
{
    /**
     * @param string $string
     * @param bool $capitalizeFirstCharacter
     *
     * @return string
     */
    public static function fromDashToCamelCase(string $string, bool $capitalizeFirstCharacter = true): string
    {
        $str = str_replace('-', '', ucwords($string, '-'));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }
}
