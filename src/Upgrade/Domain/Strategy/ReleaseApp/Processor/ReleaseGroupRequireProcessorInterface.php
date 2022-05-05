<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Processor;

use ReleaseApp\Infrastructure\Presentation\Entity\Collection\ReleaseGroupDtoCollection;
use Upgrade\Domain\Dto\Step\StepsExecutionDto;

interface ReleaseGroupRequireProcessorInterface
{
    /**
     * @return string
     */
    public function getProcessorName(): string;

    /**
     * @param \ReleaseApp\Infrastructure\Presentation\Entity\Collection\ReleaseGroupDtoCollection $requiteRequestCollection
     * @param \Upgrade\Domain\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Domain\Dto\Step\StepsExecutionDto
     */
    public function requireCollection(
        ReleaseGroupDtoCollection $requiteRequestCollection,
        StepsExecutionDto $stepsExecutionDto
    ): StepsExecutionDto;
}
