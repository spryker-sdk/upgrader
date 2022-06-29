<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Resolve\Domain\Checks\NotUnique;

use Resolve\Domain\AbstractResolveCheck;

class Constant extends AbstractResolveCheck
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'NotUnique:Constant';
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        // Business Logic
        return $this->getName() . 'logic will be here';
    }
}
