<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace TestProject\Method;

use TestCore\Method\MethodFromThirdPartyLibrary as CoreMethodFromThirdPartyLibrary;

class MethodFromThirdPartyLibrary extends CoreMethodFromThirdPartyLibrary
{
    /**
     * @return string
     */
    public function toString(): string
    {
        return 'The correct behavior because method presents in Third-party interface';
    }

    /**
     * @return string
     */
    public function toInt(): string
    {
        return 'The correct incorrect behavior, method should have project prefix';
    }
}
