<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\ComposerClient\ComposerFile\ComposerJson;

use Upgrader\Business\ComposerClient\ComposerFile\AbstractJsonReader;

class ComposerJsonReader extends AbstractJsonReader
{
    protected const FILENAME_JSON = 'composer.json';

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return self::FILENAME_JSON;
    }
}
