<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp;

use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Domain\Strategy\AbstractStrategy;

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
