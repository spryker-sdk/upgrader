<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\Composer;

use Upgrade\Domain\Strategy\AbstractStrategy;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;

class ComposerStrategy extends AbstractStrategy
{
    /**
     * @return string
     */
    public function getStrategyName(): string
    {
        return ConfigurationProvider::COMPOSER_STRATEGY;
    }
}
