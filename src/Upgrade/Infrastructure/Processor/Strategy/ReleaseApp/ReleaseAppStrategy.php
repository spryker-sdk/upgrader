<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\ReleaseApp;

use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Processor\Strategy\AbstractStrategy;

class ReleaseAppStrategy extends AbstractStrategy
{
    /**
     * @return string
     */
    public function getStrategyName(): string
    {
        return ConfigurationProvider::RELEASE_APP_STRATEGY;
    }
}
