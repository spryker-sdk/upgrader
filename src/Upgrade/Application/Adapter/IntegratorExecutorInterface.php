<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Adapter;

use Upgrade\Application\Dto\StepsResponseDto;

interface IntegratorExecutorInterface
{
    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     * @param array<\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto> $modules
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function runIntegrator(StepsResponseDto $stepsExecutionDto, array $modules = []): StepsResponseDto;

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function runIntegratorLockUpdater(StepsResponseDto $stepsExecutionDto): StepsResponseDto;
}
