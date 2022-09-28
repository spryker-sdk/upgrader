<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy;

use Upgrade\Application\Dto\StepsResponseDto;

interface StrategyInterface
{
    /**
     * @return string
     */
    public function getStrategyName(): string;

    /**
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function upgrade(): StepsResponseDto;
}
