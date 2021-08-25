<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Composer\ComposerJson;

interface ComposerJsonReaderInterface
{
    /**
     * @return array
     */
    public function read(): array;
}
