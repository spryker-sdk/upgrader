<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\ComposerClient\ComposerFile;

interface JsonReaderInterface
{
    /**
     * @return string
     */
    public function getFileName(): string;

    /**
     * @return array
     */
    public function read(): array;
}
