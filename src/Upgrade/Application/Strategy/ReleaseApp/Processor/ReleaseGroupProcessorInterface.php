<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Processor;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Dto\StepsResponseDto;

interface ReleaseGroupProcessorInterface
{
    /**
     * @return string
     */
    public function getProcessorName(): string;

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection $requiteRequestCollection
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function process(
        ReleaseGroupDtoCollection $requiteRequestCollection,
        StepsResponseDto $stepsExecutionDto
    ): StepsResponseDto;
}
