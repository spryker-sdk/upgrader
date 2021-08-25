<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Composer\Helper\JsonFile;

class JsonFileWriteHelper
{
    /**
     * @param string $path
     * @param array $body
     *
     * @return bool
     */
    public static function writeToPath(string $path, array $body): bool
    {
        $encodedJson4Spaces = json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $encodedJson2Spaces = preg_replace('/^(  +?)\\1(?=[^ ])/m', '$1', $encodedJson4Spaces) . "\n";

        return (bool)file_put_contents($path, $encodedJson2Spaces);
    }
}
