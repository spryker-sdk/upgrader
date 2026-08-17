<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor\Event;

use Upgrade\Application\Dto\StepsResponseDto;

class ReleaseGroupProcessorEvent
{
    /**
     * @var string
     */
    public const PRE_PROCESSOR = 'PRE_PROCESSOR';

    /**
     * @var string
     */
    public const POST_PROCESSOR = 'POST_PROCESSOR';

    /**
     * @var string
     */
    public const PRE_REQUIRE = 'PRE_REQUIRE';

    protected StepsResponseDto $stepsExecutionDto;

    public function __construct(StepsResponseDto $stepsExecutionDto)
    {
        $this->stepsExecutionDto = $stepsExecutionDto;
    }

    public function getStepsExecutionDto(): StepsResponseDto
    {
        return $this->stepsExecutionDto;
    }

    public function setStepsExecutionDto(StepsResponseDto $stepsExecutionDto): void
    {
        $this->stepsExecutionDto = $stepsExecutionDto;
    }
}
