<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\Report\Builder;

use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Infrastructure\Report\Dto\ReportDto;

interface ReportDtoBuilderInterface
{
    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return \Upgrade\Infrastructure\Report\Dto\ReportDto
     */
    public function buildFromStepResponseDto(StepsResponseDto $stepsResponseDto): ReportDto;
}
