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
     * @param array<\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto> $modules
     */
    public function runIntegrator(StepsResponseDto $stepsExecutionDto, array $modules = []): StepsResponseDto;

    public function runIntegratorLockUpdater(StepsResponseDto $stepsExecutionDto): StepsResponseDto;
}
