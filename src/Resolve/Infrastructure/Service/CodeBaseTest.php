<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Infrastructure\Service;

class CodeBaseTest
{
    /**
     * @return int
     */
    public function test(): int
    {
        return 1;
    }

    /**
     * @return int
     */
    public function test2(): int
    {
        return $this->test();
    }
}
