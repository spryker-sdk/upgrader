<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace PackageStorage\Application\Metric;

use Upgrade\Application\Dto\StepsResponseDto;

interface ModuleStatisticUpdaterInterface
{
    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function updateStatisticPreRequire(StepsResponseDto $stepsResponseDto): StepsResponseDto;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function updateStatisticPostRequire(StepsResponseDto $stepsResponseDto): StepsResponseDto;
}
