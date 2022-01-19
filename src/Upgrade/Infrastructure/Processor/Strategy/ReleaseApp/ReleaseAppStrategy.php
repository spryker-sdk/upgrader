<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\ReleaseApp;

use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Dto\Step\StepsExecutionDto;
use Upgrade\Infrastructure\Processor\Strategy\StrategyInterface;

class ReleaseAppStrategy implements StrategyInterface
{
    /**
     * @return string
     */
    public function getStrategyName(): string
    {
        return ConfigurationProvider::RELEASE_APP_STRATEGY;
    }

    /**
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function upgrade(): StepsExecutionDto
    {
        return new StepsExecutionDto(true);
    }
}
