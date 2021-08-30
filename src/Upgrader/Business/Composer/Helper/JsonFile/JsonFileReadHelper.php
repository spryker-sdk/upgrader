<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Composer\Helper\JsonFile;

use Exception;

class JsonFileReadHelper
{
    /**
     * @param string $path
     *
     * @throws \Exception
     *
     * @return array
     */
    public function readFromPath(string $path): array
    {
        if (!file_exists($path)) {
            throw new Exception('File is not exist: ' . $path);
        }

        $fileContent = (string)file_get_contents($path);

        return json_decode($fileContent, true);
    }
}
