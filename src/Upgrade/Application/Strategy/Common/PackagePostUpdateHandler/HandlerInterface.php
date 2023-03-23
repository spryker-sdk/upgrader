<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\PackagePostUpdateHandler;

use Upgrade\Application\Dto\StepsResponseDto;

interface HandlerInterface
{
    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return bool
     */
    public function isApplicable(StepsResponseDto $stepsExecutionDto): bool;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function handle(StepsResponseDto $stepsExecutionDto): StepsResponseDto;
}
