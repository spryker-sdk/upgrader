<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Request;

class UpgraderRequest
{
    /**
     * @var string|null
     */
    protected $strategy;

    /**
     * @param string|null $strategy
     */
    public function __construct(?string $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @return string|null
     */
    public function getStrategy(): ?string
    {
        return $this->strategy;
    }
}
