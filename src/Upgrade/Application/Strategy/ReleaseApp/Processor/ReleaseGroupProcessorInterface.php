<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Dto\StepsResponseDto;

interface ReleaseGroupProcessorInterface
{
    public function getProcessorName(): string;

    public function process(
        ReleaseGroupDtoCollection $requireRequestCollection,
        StepsResponseDto $stepsExecutionDto
    ): StepsResponseDto;
}
